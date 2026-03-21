<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Exceptions\ExceptionHandler;
use App\Services\Calendly\CalendlyClient;
use App\Services\Klaviyo\KlaviyoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Processes a Calendly webhook payload by updating the corresponding Klaviyo profile.
 */
class ProcessCalendlyWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  array<string, mixed>  $payload  The webhook payload from Calendly
     */
    public function __construct(public readonly array $payload) {}

    /**
     * Execute the job.
     */
    public function handle(CalendlyClient $calendlyClient, KlaviyoService $klaviyoService): void
    {
        $email = $this->payload['email'] ?? null;

        if ($email === null || $email === '') {
            Log::warning('Calendly webhook payload missing email', ['payload' => $this->payload]);

            return;
        }

        try {
            $profileId = $klaviyoService->findProfileIdByEmail($email);

            if ($profileId === null) {
                $profileId = $klaviyoService->createMinimalProfile($email);

                if ($profileId === null) {
                    Log::warning('Failed to find or create Klaviyo profile for Calendly invitee', ['email' => $email]);

                    return;
                }
            }

            $callProperties = $this->buildCallProperties($calendlyClient);
            $klaviyoService->updateProfileProperties($profileId, $callProperties);
        } catch (\Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    /**
     * Build the call properties array from the webhook payload and Calendly API.
     *
     * @return array<string, string|null>
     *
     * @throws ConnectionException
     */
    private function buildCallProperties(CalendlyClient $calendlyClient): array
    {
        $properties = [
            'health_check_call_booked' => 'true',
            'health_check_call_date' => $this->payload['scheduled_event']['start_time'] ?? null,
            'health_check_reschedule_url' => $this->payload['reschedule_url'] ?? null,
            'health_check_abort_url' => $this->payload['cancel_url'] ?? null,
        ];

        $eventUri = $this->payload['scheduled_event']['uri'] ?? null;

        if ($eventUri !== null) {
            $joinUrl = $this->fetchJoinUrl($calendlyClient, $eventUri);

            if ($joinUrl !== null) {
                $properties['health_check_call_url'] = $joinUrl;
            }
        }

        return array_filter($properties, fn ($value) => $value !== null);
    }

    /**
     * Fetch the join URL (Google Meet link) from the Calendly event details.
     *
     * @throws ConnectionException
     */
    private function fetchJoinUrl(CalendlyClient $calendlyClient, string $eventUri): ?string
    {
        $response = $calendlyClient->getEvent($eventUri);

        if (! $response->successful()) {
            Log::warning('Failed to fetch Calendly event details', [
                'event_uri' => $eventUri,
                'status' => $response->status(),
            ]);

            return null;
        }

        return $response->json('resource.location.join_url');
    }
}
