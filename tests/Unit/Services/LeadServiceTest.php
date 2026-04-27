<?php

use App\Models\Lead;
use App\Services\LeadService;
use Combindma\FacebookPixel\Facades\MetaPixel;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\UserData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

function consentedRequest(string $url = 'https://coine.test/contact'): Request
{
    return Request::create($url, 'POST', [], [
        'cookie_consent' => json_encode(['necessary' => true, 'marketing' => true]),
    ]);
}

test('createAndTrack creates the lead and forwards email/phone to the meta tracker', function () {
    $request = consentedRequest();

    $service = Mockery::mock(LeadService::class)->makePartial();
    $service->shouldReceive('trackMetaPixelEvent')
        ->once()
        ->with('Lead', 'lead@example.com', '+39111222333');
    $service->shouldReceive('trackGAEvent')
        ->once()
        ->with($request, 'generate_lead');
    $service->shouldReceive('trackLinkedInEvent')
        ->once()
        ->with($request, 'lead', 'lead@example.com');

    $lead = $service->createAndTrack([
        'name' => 'Tracked Lead',
        'email' => 'lead@example.com',
        'phone' => '+39111222333',
        'terms' => true,
    ], $request);

    expect($lead)->toBeInstanceOf(Lead::class);
    $this->assertDatabaseHas('leads', [
        'name' => 'Tracked Lead',
        'email' => 'lead@example.com',
        'terms' => true,
    ]);
});

test('trackMetaPixelEvent sends with enriched UserData and flashes a standard event', function () {
    $request = consentedRequest();
    $this->app->instance('request', $request);

    MetaPixel::shouldReceive('isEnabled')->once()->andReturn(true);
    MetaPixel::shouldReceive('send')
        ->once()
        ->withArgs(fn (string $eventName, string $eventId, CustomData $custom, UserData $userData): bool => $eventName === 'Lead'
            && $eventId !== ''
            && $userData instanceof UserData);
    MetaPixel::shouldReceive('flashEvent')
        ->once()
        ->withArgs(fn (string $eventName, array $payload, string $eventId): bool => $eventName === 'Lead' && $payload === [] && $eventId !== '');

    app(LeadService::class)->trackMetaPixelEvent('Lead', 'lead@example.com', '+39111222333');
});

test('trackMetaPixelEvent flags custom events in the flash payload', function () {
    $request = consentedRequest();
    $this->app->instance('request', $request);

    MetaPixel::shouldReceive('isEnabled')->once()->andReturn(true);
    MetaPixel::shouldReceive('send')->once();
    MetaPixel::shouldReceive('flashEvent')
        ->once()
        ->withArgs(fn (string $eventName, array $payload, string $eventId): bool => $eventName === 'startQuiz'
            && ($payload[LeadService::META_TRACK_METHOD_KEY] ?? null) === 'trackCustom'
            && $eventId !== '');

    app(LeadService::class)->trackMetaPixelEvent('startQuiz', null, null, true);
});

test('trackMetaPixelEvent logs exceptions and still flashes the event', function () {
    $request = consentedRequest();
    $this->app->instance('request', $request);

    MetaPixel::shouldReceive('isEnabled')->once()->andReturn(true);
    MetaPixel::shouldReceive('send')
        ->once()
        ->andThrow(new RuntimeException('meta send failed'));
    MetaPixel::shouldReceive('flashEvent')
        ->once()
        ->withArgs(fn (string $eventName, array $payload, string $eventId): bool => $eventName === 'Lead' && $payload === [] && $eventId !== '');

    Log::shouldReceive('error')
        ->once()
        ->with('meta send failed', Mockery::on(fn (array $context): bool => $context['exception'] === RuntimeException::class));

    app(LeadService::class)->trackMetaPixelEvent('Lead');
});

test('trackMetaPixelEventServerSide reuses the provided event id and does not flash', function () {
    $request = consentedRequest();
    $this->app->instance('request', $request);

    MetaPixel::shouldReceive('isEnabled')->once()->andReturn(true);
    MetaPixel::shouldReceive('send')
        ->once()
        ->withArgs(fn (string $eventName, string $eventId, CustomData $custom, UserData $userData): bool => $eventName === 'startQuiz'
            && $eventId === '11111111-2222-4333-8444-555555555555'
            && $userData instanceof UserData);
    MetaPixel::shouldReceive('flashEvent')->never();

    app(LeadService::class)->trackMetaPixelEventServerSide('startQuiz', '11111111-2222-4333-8444-555555555555');
});

test('trackMetaPixelEventServerSide is a no-op when consent is missing', function () {
    $request = Request::create('https://coine.test/health-check/start', 'POST');
    $this->app->instance('request', $request);

    MetaPixel::shouldReceive('isEnabled')->once()->andReturn(true);
    MetaPixel::shouldReceive('send')->never();

    app(LeadService::class)->trackMetaPixelEventServerSide('startQuiz', '11111111-2222-4333-8444-555555555555');
});
