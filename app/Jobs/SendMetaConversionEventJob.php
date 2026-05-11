<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\Meta\CapiClient;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\UserData;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

/**
 * Asynchronous dispatch of a single Meta Conversions API event.
 *
 * The payload travels through the queue as plain arrays so the SDK objects
 * are reconstructed inside `handle()` — that keeps the job serialization safe
 * across SDK upgrades and avoids leaking the request lifecycle into the
 * worker.
 */
class SendMetaConversionEventJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [10, 30, 60];

    public int $timeout = 15;

    /**
     * @param  array<string, mixed>  $userDataAttributes  Plain arrays that mirror
     *                                                    the setter API of
     *                                                    `FacebookAds\Object\ServerSide\UserData`.
     * @param  array<string, mixed>  $customDataAttributes  Plain arrays that mirror
     *                                                      `FacebookAds\Object\ServerSide\CustomData`.
     * @param  ?string  $eventSourceUrl  Absolute URL of the page where the event
     *                                   originated. Captured at dispatch time
     *                                   (not at send time) because the queue
     *                                   worker has no active request and would
     *                                   otherwise collapse the URL to APP_URL.
     */
    public function __construct(
        public readonly string $eventName,
        public readonly string $eventId,
        public readonly array $userDataAttributes,
        public readonly array $customDataAttributes = [],
        public readonly ?string $eventSourceUrl = null,
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(CapiClient $client): void
    {
        $client->send(
            $this->eventName,
            $this->eventId,
            self::buildCustomData($this->customDataAttributes),
            self::buildUserData($this->userDataAttributes),
            $this->eventSourceUrl,
        );
    }

    /**
     * Dispatch the event through the queue or run it inline based on the
     * `meta-pixel.queue_enabled` flag. Keeping the routing here means every
     * caller (LeadService, TrackMetaPageView, future event sites) goes through
     * the same code path and the flag flip is the single rollback switch.
     *
     * @param  array<string, mixed>  $customDataAttributes
     */
    public static function send(
        string $eventName,
        string $eventId,
        UserData $userData,
        array $customDataAttributes = [],
        ?string $eventSourceUrl = null,
    ): void {
        $job = new self(
            $eventName,
            $eventId,
            self::serializeUserData($userData),
            $customDataAttributes,
            $eventSourceUrl,
        );

        if (config('meta-pixel.queue_enabled', true)) {
            dispatch($job);

            return;
        }

        dispatch_sync($job);
    }

    /**
     * Serialize a UserData object to the plain shape we put on the queue.
     * Kept here (rather than on UserData itself) so the dependency on the SDK
     * stays unidirectional — callers only need to know about this job.
     *
     * @return array<string, mixed>
     */
    public static function serializeUserData(UserData $userData): array
    {
        $attributes = [
            'email' => $userData->getEmail(),
            'phone' => $userData->getPhone(),
            'firstName' => $userData->getFirstName(),
            'lastName' => $userData->getLastName(),
            'externalId' => $userData->getExternalId(),
            'fbp' => $userData->getFbp(),
            'fbc' => $userData->getFbc(),
            'ipAddress' => $userData->getClientIpAddress(),
            'userAgent' => $userData->getClientUserAgent(),
            'country' => $userData->getCountryCode(),
            'city' => $userData->getCity(),
            'state' => $userData->getState(),
            'zip' => $userData->getZipCode(),
        ];

        return array_filter($attributes, static fn ($value) => $value !== null && $value !== '');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private static function buildUserData(array $attributes): UserData
    {
        $userData = new UserData;

        foreach ($attributes as $key => $value) {
            match ($key) {
                'email' => $userData->setEmail($value),
                'phone' => $userData->setPhone($value),
                'firstName' => $userData->setFirstName($value),
                'lastName' => $userData->setLastName($value),
                'externalId' => $userData->setExternalId($value),
                'fbp' => $userData->setFbp($value),
                'fbc' => $userData->setFbc($value),
                'ipAddress' => $userData->setClientIpAddress($value),
                'userAgent' => $userData->setClientUserAgent($value),
                'country' => $userData->setCountryCode($value),
                'city' => $userData->setCity($value),
                'state' => $userData->setState($value),
                'zip' => $userData->setZipCode($value),
                default => null,
            };
        }

        return $userData;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private static function buildCustomData(array $attributes): CustomData
    {
        $customData = new CustomData;

        foreach ($attributes as $key => $value) {
            match ($key) {
                'currency' => $customData->setCurrency($value),
                'value' => $customData->setValue($value),
                'contentName' => $customData->setContentName($value),
                'contentCategory' => $customData->setContentCategory($value),
                'contentIds' => $customData->setContentIds($value),
                'contentType' => $customData->setContentType($value),
                default => null,
            };
        }

        return $customData;
    }
}
