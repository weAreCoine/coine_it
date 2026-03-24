<?php

declare(strict_types=1);

namespace App\Services\LinkedIn;

use Illuminate\Support\Facades\Http;

/**
 * Low-level HTTP client for the LinkedIn Conversions API.
 */
class LinkedInClient
{
    private const API_URL = 'https://api.linkedin.com/rest/conversionEvents';

    /**
     * Send a single conversion event to LinkedIn.
     *
     * @param  string  $conversionUrn  Conversion rule URN (e.g. urn:lla:llaPartnerConversion:123)
     * @param  array<int, array{idType: string, idValue: string}>  $userIds  User identifiers for matching
     * @param  array<string, string>  $userInfo  Optional user info (firstName, lastName, etc.)
     * @param  string  $eventId  Unique event ID for deduplication
     * @param  int|null  $timestamp  Conversion timestamp in milliseconds (defaults to now)
     */
    public function sendConversionEvent(
        string $conversionUrn,
        array $userIds,
        array $userInfo,
        string $eventId,
        ?int $timestamp = null,
    ): void {
        $body = [
            'conversion' => $conversionUrn,
            'conversionHappenedAt' => $timestamp ?? (int) (microtime(true) * 1000),
            'user' => [
                'userIds' => $userIds,
            ],
            'eventId' => $eventId,
        ];

        if ($userInfo !== []) {
            $body['user']['userInfo'] = $userInfo;
        }

        Http::withHeaders([
            'Authorization' => 'Bearer '.config('linkedin.access_token'),
            'Linkedin-Version' => config('linkedin.api_version'),
            'X-Restli-Protocol-Version' => '2.0.0',
        ])->post(self::API_URL, $body);
    }
}
