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
     *
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
     *
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

    /**
     * Get a profile by email address.
     *
     * @param  string  $email  The email address to search for
     *
     * @throws ConnectionException
     */
    public function getProfileByEmail(string $email): Response
    {
        return Http::withHeaders($this->headers())
            ->get('https://a.klaviyo.com/api/profiles', [
                'filter' => 'equals(email,"'.$email.'")',
            ]);
    }

    /**
     * Subscribe a profile to a Klaviyo list for email marketing.
     *
     * @param  string  $email  The email address to subscribe
     * @param  string  $listId  The Klaviyo list ID
     *
     * @throws ConnectionException
     */
    public function subscribeToList(string $email, string $listId): Response
    {
        return Http::withHeaders($this->headers())
            ->post('https://a.klaviyo.com/api/profile-subscription-bulk-create-jobs', [
                'data' => [
                    'type' => 'profile-subscription-bulk-create-job',
                    'attributes' => [
                        'profiles' => [
                            'data' => [
                                [
                                    'type' => 'profile',
                                    'attributes' => [
                                        'email' => $email,
                                        'subscriptions' => [
                                            'email' => [
                                                'marketing' => [
                                                    'consent' => 'SUBSCRIBED',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'relationships' => [
                        'list' => [
                            'data' => [
                                'type' => 'list',
                                'id' => $listId,
                            ],
                        ],
                    ],
                ],
            ]);
    }
}
