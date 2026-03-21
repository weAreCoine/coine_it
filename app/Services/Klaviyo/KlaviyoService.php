<?php

declare(strict_types=1);

namespace App\Services\Klaviyo;

use App\Exceptions\ExceptionHandler;
use App\Models\Lead;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;

/**
 * Business-logic layer for Klaviyo profile synchronization.
 */
class KlaviyoService
{
    public function __construct(private KlaviyoClient $client) {}

    /**
     * Check if Klaviyo integration is enabled and configured.
     */
    public static function isEnabled(): bool
    {
        return (bool) config('services.klaviyo.enabled')
            && config('services.klaviyo.api_key') !== '';
    }

    /**
     * Sync a lead to Klaviyo as a profile.
     *
     * Creates a new profile or updates an existing one if the email already exists (409 conflict).
     */
    public function syncLead(Lead $lead): void
    {
        $attributes = $this->mapLeadToProfileAttributes($lead);

        try {
            $response = $this->client->createProfile($attributes);

            if ($response->status() === 409) {
                $profileId = $this->extractProfileIdFromConflict($response->json());

                if ($profileId !== null) {
                    $this->client->updateProfile($profileId, $attributes);
                    Log::info('Klaviyo profile updated for lead', ['lead_id' => $lead->id]);
                    $this->subscribeLeadToList($lead);

                    return;
                }

                Log::warning('Klaviyo 409 conflict but could not extract profile ID', [
                    'lead_id' => $lead->id,
                    'response' => $response->json(),
                ]);

                return;
            }

            if ($response->successful()) {
                Log::info('Klaviyo profile created for lead', ['lead_id' => $lead->id]);
                $this->subscribeLeadToList($lead);
            } else {
                Log::warning('Klaviyo profile creation failed', [
                    'lead_id' => $lead->id,
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);
            }
        } catch (\Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    /**
     * Map a Lead model to Klaviyo profile attributes.
     *
     * @return array<string, mixed>
     */
    public function mapLeadToProfileAttributes(Lead $lead): array
    {
        $nameParts = $this->splitName($lead->name ?? '');

        $isHealthCheck = is_array($lead->quiz_answers);

        $properties = [
            'lead_source' => $isHealthCheck ? 'health_check' : 'contact_form',
        ];

        if ($isHealthCheck) {
            $properties['website'] = $lead->website ?? '';
            $properties['quiz_score'] = $lead->quiz_score ?? 0;
            $properties['health_check_call_booked'] = 'false';
            $properties['health_check_result_url'] = route('health-check.result', $lead);

            foreach ($lead->quiz_answers as $key => $value) {
                $properties['quiz_'.$key] = is_array($value) ? implode(', ', $value) : $value;
            }
        }

        return [
            'email' => $lead->email,
            'first_name' => $nameParts['first_name'],
            'last_name' => $nameParts['last_name'],
            'properties' => $properties,
        ];
    }

    /**
     * Split a full name into first and last name.
     *
     * @return array{first_name: string, last_name: string}
     */
    private function splitName(string $fullName): array
    {
        $parts = explode(' ', trim($fullName), 2);

        return [
            'first_name' => $parts[0] ?? '',
            'last_name' => $parts[1] ?? '',
        ];
    }

    /**
     * Extract the profile ID from a 409 conflict response.
     *
     * @param  array<string, mixed>|null  $responseBody
     */
    private function extractProfileIdFromConflict(?array $responseBody): ?string
    {
        return $responseBody['errors'][0]['meta']['duplicate_profile_id'] ?? null;
    }

    /**
     * Subscribe a lead to the Klaviyo email marketing list.
     *
     * @throws ConnectionException
     */
    private function subscribeLeadToList(Lead $lead): void
    {
        $listId = config('services.klaviyo.list_id');

        if ($listId === '' || $listId === null) {
            Log::warning('Klaviyo list_id not configured, skipping subscription', ['lead_id' => $lead->id]);

            return;
        }

        $response = $this->client->subscribeToList($lead->email, $listId);

        if ($response->successful()) {
            Log::info('Klaviyo email subscription created for lead', ['lead_id' => $lead->id]);
        } else {
            Log::warning('Klaviyo email subscription failed', [
                'lead_id' => $lead->id,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);
        }
    }

    /**
     * Find a Klaviyo profile ID by email address.
     *
     * @param  string  $email  The email address to search for
     * @return string|null The profile ID, or null if not found
     *
     * @throws ConnectionException
     */
    public function findProfileIdByEmail(string $email): ?string
    {
        $response = $this->client->getProfileByEmail($email);

        if (! $response->successful()) {
            Log::warning('Klaviyo profile lookup failed', [
                'email' => $email,
                'status' => $response->status(),
            ]);

            return null;
        }

        $profiles = $response->json('data', []);

        if (count($profiles) === 0) {
            Log::info('No Klaviyo profile found for email, skipping call properties update', [
                'email' => $email,
            ]);

            return null;
        }

        return $profiles[0]['id'];
    }

    /**
     * Create a minimal Klaviyo profile with just an email address.
     *
     * @return string|null The profile ID, or null on failure
     *
     * @throws ConnectionException
     */
    public function createMinimalProfile(string $email): ?string
    {
        $response = $this->client->createProfile(['email' => $email]);

        if ($response->status() === 409) {
            return $this->extractProfileIdFromConflict($response->json());
        }

        if ($response->successful()) {
            Log::info('Klaviyo minimal profile created', ['email' => $email]);

            return $response->json('data.id');
        }

        Log::warning('Failed to create minimal Klaviyo profile', [
            'email' => $email,
            'status' => $response->status(),
        ]);

        return null;
    }

    /**
     * Update custom properties on a Klaviyo profile.
     *
     * @param  string  $profileId  The Klaviyo profile ID
     * @param  array<string, mixed>  $properties  Properties to set on the profile
     *
     * @throws ConnectionException
     */
    public function updateProfileProperties(string $profileId, array $properties): void
    {
        $response = $this->client->updateProfile($profileId, [
            'properties' => $properties,
        ]);

        if ($response->successful()) {
            Log::info('Klaviyo profile updated with call properties', [
                'profile_id' => $profileId,
            ]);
        } else {
            Log::warning('Failed to update Klaviyo profile with call properties', [
                'profile_id' => $profileId,
                'status' => $response->status(),
                'response' => $response->json(),
            ]);
        }
    }
}
