<?php

declare(strict_types=1);

namespace App\Services\Calendly;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Low-level HTTP client for the Calendly API.
 */
class CalendlyClient
{
    /**
     * Get event details from Calendly.
     *
     * @param  string  $eventUri  The full URI of the scheduled event
     *
     * @throws ConnectionException
     */
    public function getEvent(string $eventUri): Response
    {
        return Http::withHeaders($this->headers())
            ->get($eventUri);
    }

    /**
     * Get the current user's details (including organization URI).
     *
     * @throws ConnectionException
     */
    public function getCurrentUser(): Response
    {
        return Http::withHeaders($this->headers())
            ->get('https://api.calendly.com/users/me');
    }

    /**
     * List webhook subscriptions for an organization.
     *
     * @param  string  $organizationUri  The full URI of the organization
     *
     * @throws ConnectionException
     */
    public function listWebhookSubscriptions(string $organizationUri): Response
    {
        return Http::withHeaders($this->headers())
            ->get('https://api.calendly.com/webhook_subscriptions', [
                'organization' => $organizationUri,
                'scope' => 'organization',
            ]);
    }

    /**
     * Create a webhook subscription.
     *
     * @param  string  $url  The URL to receive webhook events
     * @param  array<int, string>  $events  The event types to subscribe to
     * @param  string  $organizationUri  The full URI of the organization
     * @param  string  $signingKey  The signing key for webhook signature verification
     *
     * @throws ConnectionException
     */
    public function createWebhookSubscription(
        string $url,
        array $events,
        string $organizationUri,
        string $signingKey,
    ): Response {
        return Http::withHeaders($this->headers())
            ->post('https://api.calendly.com/webhook_subscriptions', [
                'url' => $url,
                'events' => $events,
                'organization' => $organizationUri,
                'scope' => 'organization',
                'signing_key' => $signingKey,
            ]);
    }

    /**
     * Delete a webhook subscription.
     *
     * @param  string  $webhookUri  The full URI of the webhook subscription
     *
     * @throws ConnectionException
     */
    public function deleteWebhookSubscription(string $webhookUri): Response
    {
        return Http::withHeaders($this->headers())
            ->delete($webhookUri);
    }

    /**
     * @return array<string, string>
     */
    private function headers(): array
    {
        return [
            'Authorization' => 'Bearer '.config('services.calendly.pat'),
            'Content-Type' => 'application/json',
        ];
    }
}
