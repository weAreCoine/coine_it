<?php

declare(strict_types=1);

namespace App\Services\Meta;

use Combindma\FacebookPixel\Facades\MetaPixel;
use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\ServerSide\ActionSource;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use FacebookAds\Object\ServerSide\EventResponse;
use FacebookAds\Object\ServerSide\UserData;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/**
 * Direct wrapper around the Facebook Business SDK that sends a single
 * Conversions API event and records the outcome on the dedicated
 * `meta-capi` log channel.
 *
 * The bundled `MetaPixel::send()` helper from `combindma/laravel-facebook-pixel`
 * is intentionally bypassed because it builds the event with
 * `URL::current()` at send-time. When the call is queued, the worker has no
 * active request, so the URL collapses to APP_URL and every server event ends
 * up reporting the home page as `event_source_url`. Bypassing the helper lets
 * us pass the original request URL captured at dispatch time.
 */
class CapiClient
{
    /**
     * Send the event to Meta CAPI and write a structured summary of the
     * response to the `meta-capi` channel. Exceptions are caught here so the
     * caller can stay focused on building the payload.
     *
     * @throws Throwable
     */
    public function send(
        string $eventName,
        string $eventId,
        CustomData $customData,
        UserData $userData,
        ?string $eventSourceUrl = null,
    ): ?EventResponse {
        if (! MetaPixel::isEnabled()) {
            return null;
        }

        $token = MetaPixel::token();

        if ($token === null || $token === '') {
            throw new RuntimeException('Meta CAPI token is not configured.');
        }

        $api = Api::init(null, null, $token);

        if (config('meta-pixel.logging')) {
            $api->setLogger(new CurlLogger);
        }

        $event = (new Event)
            ->setEventName($eventName)
            ->setEventTime(time())
            ->setEventId($eventId)
            ->setUserData($userData)
            ->setCustomData($customData)
            ->setActionSource(ActionSource::WEBSITE);

        if ($eventSourceUrl !== null && $eventSourceUrl !== '') {
            $event->setEventSourceUrl($eventSourceUrl);
        }

        $request = new EventRequest(MetaPixel::pixelId())->setEvents([$event]);

        if (MetaPixel::testEnabled()) {
            $request->setTestEventCode(config('meta-pixel.test_event_code'));
        }

        $startedAt = microtime(true);

        try {
            $response = $request->execute();

            Log::channel('meta-capi')->info('Meta CAPI event sent', [
                'event_name' => $eventName,
                'event_id' => $eventId,
                'event_source_url' => $eventSourceUrl,
                'events_received' => $response?->getEventsReceived(),
                'fbtrace_id' => $response?->getFbTraceId(),
                'messages' => $response?->getMessages(),
                'duration_ms' => self::elapsedMs($startedAt),
            ]);

            return $response;
        } catch (Throwable $e) {
            Log::channel('meta-capi')->error('Meta CAPI event failed', [
                'event_name' => $eventName,
                'event_id' => $eventId,
                'event_source_url' => $eventSourceUrl,
                'error' => $e->getMessage(),
                'duration_ms' => self::elapsedMs($startedAt),
            ]);

            throw $e;
        }
    }

    private static function elapsedMs(float $startedAt): int
    {
        return (int) round((microtime(true) - $startedAt) * 1000);
    }
}
