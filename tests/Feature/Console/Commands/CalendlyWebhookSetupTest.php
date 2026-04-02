<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config([
        'services.calendly.pat' => 'test-pat',
        'services.calendly.webhook_signing_key' => 'test-signing-key',
        'app.url' => 'https://example.com',
    ]);
});

it('fails when CALENDLY_PAT is not configured', function () {
    config(['services.calendly.pat' => '']);

    $this->artisan('calendly:webhook-setup')
        ->expectsOutputToContain('CALENDLY_PAT is not configured')
        ->assertFailed();
});

it('fails when CALENDLY_WEBHOOK_SIGNING_KEY is not configured', function () {
    config(['services.calendly.webhook_signing_key' => '']);

    Http::fake([
        'api.calendly.com/users/me' => Http::response([
            'resource' => ['current_organization' => 'https://api.calendly.com/organizations/org-1'],
        ], 200),
    ]);

    $this->artisan('calendly:webhook-setup')
        ->expectsOutputToContain('CALENDLY_WEBHOOK_SIGNING_KEY is not configured')
        ->assertFailed();
});

it('creates webhook subscription when none exists', function () {
    Http::fake([
        'api.calendly.com/users/me' => Http::response([
            'resource' => ['current_organization' => 'https://api.calendly.com/organizations/org-1'],
        ], 200),
        'api.calendly.com/webhook_subscriptions*' => Http::sequence()
            ->push(['collection' => []], 200)
            ->push(['resource' => ['uri' => 'https://api.calendly.com/webhook_subscriptions/wh-1']], 201),
    ]);

    $this->artisan('calendly:webhook-setup')
        ->expectsOutputToContain('Webhook subscription created')
        ->assertSuccessful();

    Http::assertSent(function ($request) {
        return $request->method() === 'POST'
            && $request->url() === 'https://api.calendly.com/webhook_subscriptions'
            && $request['url'] === 'https://example.com/api/webhooks/calendly'
            && $request['events'] === ['invitee.created']
            && $request['signing_key'] === 'test-signing-key';
    });
});

it('skips creation when webhook already exists for the URL', function () {
    Http::fake([
        'api.calendly.com/users/me' => Http::response([
            'resource' => ['current_organization' => 'https://api.calendly.com/organizations/org-1'],
        ], 200),
        'api.calendly.com/webhook_subscriptions*' => Http::response([
            'collection' => [[
                'uri' => 'https://api.calendly.com/webhook_subscriptions/wh-1',
                'callback_url' => 'https://example.com/api/webhooks/calendly',
                'events' => ['invitee.created'],
                'state' => 'active',
            ]],
        ], 200),
    ]);

    $this->artisan('calendly:webhook-setup')
        ->expectsOutputToContain('already exists')
        ->assertSuccessful();

    Http::assertNotSent(fn ($request) => $request->method() === 'POST');
});

it('lists existing webhook subscriptions', function () {
    Http::fake([
        'api.calendly.com/users/me' => Http::response([
            'resource' => ['current_organization' => 'https://api.calendly.com/organizations/org-1'],
        ], 200),
        'api.calendly.com/webhook_subscriptions*' => Http::response([
            'collection' => [[
                'uri' => 'https://api.calendly.com/webhook_subscriptions/wh-1',
                'callback_url' => 'https://example.com/api/webhooks/calendly',
                'events' => ['invitee.created'],
                'state' => 'active',
            ]],
        ], 200),
    ]);

    $this->artisan('calendly:webhook-setup --list')
        ->expectsTable(
            ['URI', 'URL', 'Events', 'State'],
            [[
                'https://api.calendly.com/webhook_subscriptions/wh-1',
                'https://example.com/api/webhooks/calendly',
                'invitee.created',
                'active',
            ]],
        )
        ->assertSuccessful();
});

it('deletes existing webhook subscription', function () {
    Http::fake([
        'api.calendly.com/users/me' => Http::response([
            'resource' => ['current_organization' => 'https://api.calendly.com/organizations/org-1'],
        ], 200),
        'api.calendly.com/webhook_subscriptions?*' => Http::response([
            'collection' => [[
                'uri' => 'https://api.calendly.com/webhook_subscriptions/wh-1',
                'callback_url' => 'https://example.com/api/webhooks/calendly',
                'events' => ['invitee.created'],
                'state' => 'active',
            ]],
        ], 200),
        'api.calendly.com/webhook_subscriptions/wh-1' => Http::response(null, 204),
    ]);

    $this->artisan('calendly:webhook-setup --delete')
        ->expectsOutputToContain('Webhook subscription deleted')
        ->assertSuccessful();

    Http::assertSent(fn ($request) => $request->method() === 'DELETE');
});

it('uses custom URL when provided', function () {
    Http::fake([
        'api.calendly.com/users/me' => Http::response([
            'resource' => ['current_organization' => 'https://api.calendly.com/organizations/org-1'],
        ], 200),
        'api.calendly.com/webhook_subscriptions*' => Http::sequence()
            ->push(['collection' => []], 200)
            ->push(['resource' => ['uri' => 'https://api.calendly.com/webhook_subscriptions/wh-1']], 201),
    ]);

    $this->artisan('calendly:webhook-setup --url=https://custom.com/webhook')
        ->assertSuccessful();

    Http::assertSent(function ($request) {
        return $request->method() === 'POST'
            && $request['url'] === 'https://custom.com/webhook';
    });
});
