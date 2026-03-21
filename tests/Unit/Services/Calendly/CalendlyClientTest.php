<?php

declare(strict_types=1);

use App\Services\Calendly\CalendlyClient;
use Illuminate\Support\Facades\Http;

uses(Tests\TestCase::class);

beforeEach(function () {
    config([
        'services.calendly.pat' => 'test-calendly-pat',
    ]);
});

it('sends a GET request to fetch event details with correct authorization header', function () {
    Http::fake([
        'api.calendly.com/scheduled_events/*' => Http::response([
            'resource' => [
                'uri' => 'https://api.calendly.com/scheduled_events/event-123',
                'location' => [
                    'type' => 'google_conference',
                    'join_url' => 'https://meet.google.com/abc-defg-hij',
                ],
            ],
        ], 200),
    ]);

    $client = new CalendlyClient;
    $response = $client->getEvent('https://api.calendly.com/scheduled_events/event-123');

    expect($response->successful())->toBeTrue()
        ->and($response->json('resource.location.join_url'))->toBe('https://meet.google.com/abc-defg-hij');

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.calendly.com/scheduled_events/event-123'
            && $request->method() === 'GET'
            && $request->header('Authorization')[0] === 'Bearer test-calendly-pat'
            && $request->header('Content-Type')[0] === 'application/json';
    });
});
