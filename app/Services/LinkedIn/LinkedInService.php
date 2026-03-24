<?php

declare(strict_types=1);

namespace App\Services\LinkedIn;

use App\Exceptions\ExceptionHandler;
use App\Helpers\CookieConsent;
use Illuminate\Http\Request;

/**
 * Business-logic layer for LinkedIn tracking (Insight Tag config + Conversions API).
 */
class LinkedInService
{
    /**
     * Check if LinkedIn tracking is enabled.
     */
    public static function isEnabled(): bool
    {
        return (bool) config('linkedin.enabled')
            && config('linkedin.partner_id') !== ''
            && config('linkedin.access_token') !== '';
    }

    /**
     * Get the LinkedIn Insight Tag Partner ID.
     */
    public static function partnerId(): string
    {
        return (string) config('linkedin.partner_id');
    }

    /**
     * Send a conversion event via LinkedIn Conversions API.
     *
     * @param  string  $conversionType  Config key for the conversion (e.g. 'lead', 'complete_registration')
     * @param  string|null  $email  User email for SHA256 matching
     */
    public static function trackConversion(Request $request, string $conversionType, ?string $email = null): void
    {
        if (! self::isEnabled() || ! CookieConsent::hasMarketingConsent()) {
            return;
        }

        $conversionUrn = (string) config("linkedin.conversions.{$conversionType}");

        if ($conversionUrn === '') {
            return;
        }

        $userIds = [];

        if ($email !== null && $email !== '') {
            $userIds[] = [
                'idType' => 'SHA256_EMAIL',
                'idValue' => hash('sha256', strtolower(trim($email))),
            ];
        }

        if ($userIds === []) {
            return;
        }

        $eventId = (string) \Illuminate\Support\Str::uuid();

        try {
            $client = app(LinkedInClient::class);
            $client->sendConversionEvent($conversionUrn, $userIds, [], $eventId);
        } catch (\Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    /**
     * Store a LinkedIn flash event in the session (for client-side tracking after redirect).
     */
    public static function flashEvent(string $conversionType): void
    {
        $conversionUrn = (string) config("linkedin.conversions.{$conversionType}");

        if ($conversionUrn === '') {
            return;
        }

        $events = session()->get('linkedin_flash_events', []);
        $events[] = [
            'conversionType' => $conversionType,
        ];
        session()->put('linkedin_flash_events', $events);
    }
}
