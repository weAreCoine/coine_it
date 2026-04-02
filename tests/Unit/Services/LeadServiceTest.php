<?php

use App\Models\Lead;
use App\Services\LeadService;
use FacebookAds\Object\ServerSide\CustomData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Combindma\FacebookPixel\Facades\MetaPixel;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

function consentedRequest(string $url = 'https://coine.test/contact'): Request
{
    return Request::create($url, 'POST', [], [
        'cookie_consent' => json_encode(['necessary' => true, 'marketing' => true]),
    ]);
}

test('createAndTrack creates the lead and dispatches all tracking methods', function () {
    $request = consentedRequest();

    $service = Mockery::mock(LeadService::class)->makePartial();
    $service->shouldReceive('trackMetaPixelEvent')
        ->once()
        ->with('Lead');
    $service->shouldReceive('trackGAEvent')
        ->once()
        ->with($request, 'generate_lead');
    $service->shouldReceive('trackLinkedInEvent')
        ->once()
        ->with($request, 'lead', 'lead@example.com');

    $lead = $service->createAndTrack([
        'name' => 'Tracked Lead',
        'email' => 'lead@example.com',
        'terms' => true,
    ], $request);

    expect($lead)->toBeInstanceOf(Lead::class);
    $this->assertDatabaseHas('leads', [
        'name' => 'Tracked Lead',
        'email' => 'lead@example.com',
        'terms' => true,
    ]);
});

test('trackMetaPixelEvent sends and flashes the event when consent is granted', function () {
    $request = consentedRequest();
    $this->app->instance('request', $request);

    MetaPixel::shouldReceive('isEnabled')->once()->andReturn(true);
    MetaPixel::shouldReceive('send')
        ->once()
        ->withArgs(fn (string $eventName, string $eventId, CustomData $data): bool => $eventName === 'Lead' && $eventId !== '');
    MetaPixel::shouldReceive('flashEvent')
        ->once()
        ->withArgs(fn (string $eventName, array $payload, string $eventId): bool => $eventName === 'Lead' && $payload === [] && $eventId !== '');

    app(LeadService::class)->trackMetaPixelEvent('Lead');
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
