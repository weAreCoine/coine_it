<?php

declare(strict_types=1);

namespace App\Services\Klaviyo;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Low-level HTTP client for the Klaviyo API.
 */
class KlaviyoClient
{
    /**
     * Create a new profile in Klaviyo.
     *
     * @param  array<string, mixed>  $attributes  Profile attributes (email, first_name, properties, etc.)
     * @throws ConnectionException
     * @throws ConnectionException
     */
    public function createProfile(array $attributes): Response
    {
        return Http::withHeaders($this->headers())
            ->post('https://a.klaviyo.com/api/profiles/', [
                'data' => [
                    'type' => 'profile',
                    'attributes' => $attributes,
                ],
            ]);
    }

    /**
     * @return array<string, string>
     */
    private function headers(): array
    {
        return [
            'Authorization' => 'Klaviyo-API-Key '.config('services.klaviyo.api_key'),
            'revision' => config('services.klaviyo.revision'),
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ];
    }

    /**
     * Update an existing profile in Klaviyo.
     *
     * @param  string  $profileId  The Klaviyo profile ID
     * @param  array<string, mixed>  $attributes  Profile attributes to update
     * @throws ConnectionException
     * @throws ConnectionException
     */
    public function updateProfile(string $profileId, array $attributes): Response
    {
        return Http::withHeaders($this->headers())
            ->patch("https://a.klaviyo.com/api/profiles/{$profileId}", [
                'data' => [
                    'type' => 'profile',
                    'id' => $profileId,
                    'attributes' => $attributes,
                ],
            ]);
    }
}
