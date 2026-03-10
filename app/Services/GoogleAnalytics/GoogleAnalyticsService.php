<?php

declare(strict_types=1);

namespace App\Services\GoogleAnalytics;

use App\Exceptions\ExceptionHandler;
use App\Helpers\CookieConsent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Business-logic layer for GA4 tracking (client-side config + server-side Measurement Protocol).
 */
class GoogleAnalyticsService
{
    private const GA_COOKIE_NAME = '_ga';

    /**
     * Check if GA4 tracking is enabled.
     */
    public static function isEnabled(): bool
    {
        return (bool) config('google-analytics.enabled')
            && config('google-analytics.measurement_id') !== ''
            && config('google-analytics.api_secret') !== '';
    }

    /**
     * Get the GA4 Measurement ID.
     */
    public static function measurementId(): string
    {
        return (string) config('google-analytics.measurement_id');
    }

    /**
     * Extract the GA client_id from the _ga cookie.
     *
     * The _ga cookie has the format: GA1.1.XXXXXXXXXX.XXXXXXXXXX
     * The client_id is the last two numeric segments joined by a dot.
     */
    public static function extractClientId(Request $request): ?string
    {
        $gaCookie = $request->cookie(self::GA_COOKIE_NAME);

        if ($gaCookie === null) {
            return null;
        }

        $parts = explode('.', $gaCookie);

        // The client_id is the last two parts (timestamp_high.timestamp_low)
        if (count($parts) >= 4) {
            return $parts[2].'.'.$parts[3];
        }

        return null;
    }

    /**
     * Send a page_view event via Measurement Protocol.
     */
    public static function trackPageView(Request $request): void
    {
        if (! self::shouldTrack($request)) {
            return;
        }

        $clientId = self::extractClientId($request);

        if ($clientId === null) {
            return;
        }

        try {
            $client = app(GoogleAnalyticsClient::class);
            $client->sendEvent($clientId, 'page_view', [
                'page_location' => $request->fullUrl(),
                'page_title' => '',
            ]);
        } catch (\Exception $e) {
            Log::error('GA4 Measurement Protocol page_view failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Send a generate_lead event via Measurement Protocol.
     *
     * @param  array<string, mixed>  $params  Additional event parameters
     */
    public static function trackGenerateLead(Request $request, array $params = []): void
    {
        if (! self::shouldTrack($request)) {
            return;
        }

        $clientId = self::extractClientId($request);

        if ($clientId === null) {
            return;
        }

        try {
            $client = app(GoogleAnalyticsClient::class);
            $client->sendEvent($clientId, 'generate_lead', $params);
        } catch (\Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    /**
     * Store a GA4 flash event in the session (for client-side tracking after redirect).
     *
     * @param  array<string, mixed>  $params
     */
    public static function flashEvent(string $eventName, array $params = []): void
    {
        $events = session()->get('ga4_flash_events', []);
        $events[] = [
            'eventName' => $eventName,
            'params' => $params,
        ];
        session()->put('ga4_flash_events', $events);
    }

    /**
     * Determine if we should send server-side events.
     */
    private static function shouldTrack(Request $request): bool
    {
        return self::isEnabled() && CookieConsent::hasMarketingConsent();
    }
}
