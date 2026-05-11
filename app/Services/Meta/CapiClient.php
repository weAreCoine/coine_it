<?php

declare(strict_types=1);

namespace App\Services\Meta;

use Combindma\FacebookPixel\Facades\MetaPixel;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\EventResponse;
use FacebookAds\Object\ServerSide\UserData;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Thin wrapper around `MetaPixel::send()` that records the outcome of every
 * Conversions API call on a dedicated `meta-capi` log channel so EMQ drops
 * and SDK failures can be diagnosed without crawling the main app log.
 *
 * Wrapping the facade — rather than calling it directly from the job — also
 * gives us a single place to swap SDKs or batch events in the future without
 * touching the callers.
 */
class CapiClient
{
    /**
     * Send the event to Meta CAPI and write a structured summary of the
     * response to the `meta-capi` channel. Exceptions are caught here so the
     * caller can stay focused on building the payload.
     */
    public function send(
        string $eventName,
        string $eventId,
        CustomData $customData,
        UserData $userData,
    ): ?EventResponse {
        $startedAt = microtime(true);

        try {
            $response = MetaPixel::send($eventName, $eventId, $customData, $userData);

            Log::channel('meta-capi')->info('Meta CAPI event sent', [
                'event_name' => $eventName,
                'event_id' => $eventId,
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
