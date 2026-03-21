<?php

declare(strict_types=1);

use App\Jobs\ProcessCalendlyWebhook;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    config([
        'services.klaviyo.api_key' => 'test-klaviyo-key',
        'services.klaviyo.revision' => '2024-10-15',
        'services.calendly.pat' => 'test-calendly-pat',
    ]);
});

it('updates klaviyo profile with call properties when profile exists', function () {
    Http::fake([
        'a.klaviyo.com/api/profiles?*' => Http::response([
            'data' => [['id' => 'profile-abc', 'type' => 'profile']],
        ], 200),
        'a.klaviyo.com/api/profiles/profile-abc' => Http::response(['data' => ['id' => 'profile-abc']], 200),
        'api.calendly.com/scheduled_events/event-123' => Http::response([
            'resource' => [
                'location' => [
                    'type' => 'google_conference',
                    'join_url' => 'https://meet.google.com/abc-defg-hij',
                ],
            ],
        ], 200),
    ]);

    $payload = [
        'email' => 'test@example.com',
        'name' => 'John Doe',
        'cancel_url' => 'https://calendly.com/cancellations/abc',
        'reschedule_url' => 'https://calendly.com/reschedulings/abc',
        'scheduled_event' => [
            'uri' => 'https://api.calendly.com/scheduled_events/event-123',
            'start_time' => '2026-04-01T10:00:00.000000Z',
        ],
    ];

    ProcessCalendlyWebhook::dispatchSync($payload);

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.calendly.com/scheduled_events/event-123'
            && $request->method() === 'GET'
            && $request->header('Authorization')[0] === 'Bearer test-calendly-pat';
    });

    Http::assertSent(function ($request) {
        if ($request->method() !== 'PATCH' || ! str_contains($request->url(), 'profile-abc')) {
            return false;
        }

        $properties = $request['data']['attributes']['properties'] ?? [];

        return $properties['health_check_call_date'] === '2026-04-01T10:00:00.000000Z'
            && $properties['health_check_call_url'] === 'https://meet.google.com/abc-defg-hij'
            && $properties['health_check_reschedule_url'] === 'https://calendly.com/reschedulings/abc'
            && $properties['health_check_abort_url'] === 'https://calendly.com/cancellations/abc';
    });
});

it('skips update when no klaviyo profile is found', function () {
    Http::fake([
        'a.klaviyo.com/api/profiles?*' => Http::response(['data' => []], 200),
    ]);

    $payload = [
        'email' => 'unknown@example.com',
        'scheduled_event' => [
            'uri' => 'https://api.calendly.com/scheduled_events/event-123',
            'start_time' => '2026-04-01T10:00:00.000000Z',
        ],
    ];

    ProcessCalendlyWebhook::dispatchSync($payload);

    Http::assertSent(fn ($request) => str_contains($request->url(), 'a.klaviyo.com/api/profiles'));
    Http::assertNotSent(fn ($request) => $request->method() === 'PATCH');
    Http::assertNotSent(fn ($request) => str_contains($request->url(), 'api.calendly.com'));
});

it('logs warning and skips when email is missing from payload', function () {
    Http::fake();

    Log::shouldReceive('warning')
        ->once()
        ->withArgs(fn ($message) => str_contains($message, 'missing email'));

    ProcessCalendlyWebhook::dispatchSync([
        'scheduled_event' => [
            'uri' => 'https://api.calendly.com/scheduled_events/event-123',
            'start_time' => '2026-04-01T10:00:00.000000Z',
        ],
    ]);

    Http::assertNothingSent();
});

it('updates profile without join url when calendly event fetch fails', function () {
    Http::fake([
        'a.klaviyo.com/api/profiles?*' => Http::response([
            'data' => [['id' => 'profile-abc', 'type' => 'profile']],
        ], 200),
        'a.klaviyo.com/api/profiles/profile-abc' => Http::response(['data' => ['id' => 'profile-abc']], 200),
        'api.calendly.com/scheduled_events/*' => Http::response([], 404),
    ]);

    $payload = [
        'email' => 'test@example.com',
        'cancel_url' => 'https://calendly.com/cancellations/abc',
        'reschedule_url' => 'https://calendly.com/reschedulings/abc',
        'scheduled_event' => [
            'uri' => 'https://api.calendly.com/scheduled_events/event-123',
            'start_time' => '2026-04-01T10:00:00.000000Z',
        ],
    ];

    ProcessCalendlyWebhook::dispatchSync($payload);

    Http::assertSent(function ($request) {
        if ($request->method() !== 'PATCH' || ! str_contains($request->url(), 'profile-abc')) {
            return false;
        }

        $properties = $request['data']['attributes']['properties'] ?? [];

        return isset($properties['health_check_call_date'])
            && isset($properties['health_check_reschedule_url'])
            && isset($properties['health_check_abort_url'])
            && ! isset($properties['health_check_call_url']);
    });
});
