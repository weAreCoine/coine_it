<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Calendly\CalendlyClient;
use Illuminate\Console\Command;

/**
 * Manages Calendly webhook subscription for receiving invitee events.
 */
class CalendlyWebhookSetup extends Command
{
    /**
     * @var string
     */
    protected $signature = 'calendly:webhook-setup
        {--url= : The webhook URL (defaults to APP_URL/api/webhooks/calendly)}
        {--delete : Delete existing webhook subscription instead of creating}
        {--list : List existing webhook subscriptions}';

    /**
     * @var string
     */
    protected $description = 'Set up Calendly webhook subscription for invitee.created events';

    /**
     * Execute the console command.
     */
    public function handle(CalendlyClient $client): int
    {
        $pat = config('services.calendly.pat');
        $signingKey = config('services.calendly.webhook_signing_key');

        if ($pat === '' || $pat === null) {
            $this->error('CALENDLY_PAT is not configured.');

            return self::FAILURE;
        }

        $organizationUri = $this->resolveOrganizationUri($client);

        if ($organizationUri === null) {
            return self::FAILURE;
        }

        if ($this->option('list')) {
            return $this->listSubscriptions($client, $organizationUri);
        }

        if ($this->option('delete')) {
            return $this->deleteSubscription($client, $organizationUri);
        }

        if ($signingKey === '' || $signingKey === null) {
            $this->error('CALENDLY_WEBHOOK_SIGNING_KEY is not configured.');

            return self::FAILURE;
        }

        return $this->createSubscription($client, $organizationUri, $signingKey);
    }

    /**
     * Resolve the organization URI from the Calendly API.
     */
    private function resolveOrganizationUri(CalendlyClient $client): ?string
    {
        $response = $client->getCurrentUser();

        if (! $response->successful()) {
            $this->error('Failed to fetch Calendly user info. Check CALENDLY_PAT.');

            return null;
        }

        return $response->json('resource.current_organization');
    }

    /**
     * List existing webhook subscriptions.
     */
    private function listSubscriptions(CalendlyClient $client, string $organizationUri): int
    {
        $response = $client->listWebhookSubscriptions($organizationUri);

        if (! $response->successful()) {
            $this->error('Failed to fetch webhook subscriptions.');

            return self::FAILURE;
        }

        $subscriptions = $response->json('collection', []);

        if (count($subscriptions) === 0) {
            $this->info('No webhook subscriptions found.');

            return self::SUCCESS;
        }

        $this->table(
            ['URI', 'URL', 'Events', 'State'],
            collect($subscriptions)->map(fn (array $sub) => [
                $sub['uri'],
                $sub['callback_url'],
                implode(', ', $sub['events']),
                $sub['state'],
            ])->toArray(),
        );

        return self::SUCCESS;
    }

    /**
     * Create a webhook subscription (idempotent: skips if already exists for the URL).
     */
    private function createSubscription(CalendlyClient $client, string $organizationUri, string $signingKey): int
    {
        $webhookUrl = $this->resolveWebhookUrl();
        $events = ['invitee.created'];

        $existing = $this->findExistingSubscription($client, $organizationUri, $webhookUrl);

        if ($existing !== null) {
            $this->info("Webhook subscription already exists for {$webhookUrl} (state: {$existing['state']}).");

            return self::SUCCESS;
        }

        $response = $client->createWebhookSubscription($webhookUrl, $events, $organizationUri, $signingKey);

        if (! $response->successful()) {
            $this->error('Failed to create webhook subscription.');
            $this->error((string) json_encode($response->json(), JSON_PRETTY_PRINT));

            return self::FAILURE;
        }

        $this->info("Webhook subscription created for {$webhookUrl}.");

        return self::SUCCESS;
    }

    /**
     * Delete existing webhook subscription for the configured URL.
     */
    private function deleteSubscription(CalendlyClient $client, string $organizationUri): int
    {
        $webhookUrl = $this->resolveWebhookUrl();
        $existing = $this->findExistingSubscription($client, $organizationUri, $webhookUrl);

        if ($existing === null) {
            $this->info("No webhook subscription found for {$webhookUrl}.");

            return self::SUCCESS;
        }

        $response = $client->deleteWebhookSubscription($existing['uri']);

        if (! $response->successful()) {
            $this->error('Failed to delete webhook subscription.');

            return self::FAILURE;
        }

        $this->info("Webhook subscription deleted for {$webhookUrl}.");

        return self::SUCCESS;
    }

    /**
     * Find an existing webhook subscription matching the given URL.
     *
     * @return array<string, mixed>|null
     */
    private function findExistingSubscription(CalendlyClient $client, string $organizationUri, string $webhookUrl): ?array
    {
        $response = $client->listWebhookSubscriptions($organizationUri);

        if (! $response->successful()) {
            return null;
        }

        $subscriptions = $response->json('collection', []);

        foreach ($subscriptions as $subscription) {
            if ($subscription['callback_url'] === $webhookUrl) {
                return $subscription;
            }
        }

        return null;
    }

    /**
     * Resolve the webhook URL from the --url option or the default route.
     */
    private function resolveWebhookUrl(): string
    {
        $url = $this->option('url');

        if ($url !== null && $url !== '') {
            return $url;
        }

        return rtrim((string) config('app.url'), '/').'/api/webhooks/calendly';
    }
}
