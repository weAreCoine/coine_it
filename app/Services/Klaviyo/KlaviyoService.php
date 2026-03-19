<?php

declare(strict_types=1);

namespace App\Services\Klaviyo;

use App\Exceptions\ExceptionHandler;
use App\Models\Lead;
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
     * Sync a health check lead to Klaviyo as a profile.
     *
     * Creates a new profile or updates an existing one if the email already exists (409 conflict).
     */
    public function syncHealthCheckLead(Lead $lead): void
    {
        $attributes = $this->mapLeadToProfileAttributes($lead);

        try {
            $response = $this->client->createProfile($attributes);

            if ($response->status() === 409) {
                $profileId = $this->extractProfileIdFromConflict($response->json());

                if ($profileId !== null) {
                    $this->client->updateProfile($profileId, $attributes);
                    Log::info('Klaviyo profile updated for lead', ['lead_id' => $lead->id]);

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

        $properties = [
            'website' => $lead->website ?? '',
            'quiz_score' => $lead->quiz_score ?? 0,
            'lead_source' => 'health_check',
        ];

        if (is_array($lead->quiz_answers)) {
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
     * Extract the profile ID from a 409 conflict response.
     *
     * @param  array<string, mixed>|null  $responseBody
     */
    private function extractProfileIdFromConflict(?array $responseBody): ?string
    {
        return $responseBody['errors'][0]['meta']['duplicate_profile_id'] ?? null;
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
}
