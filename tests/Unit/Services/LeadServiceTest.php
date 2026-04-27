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

test('createAndTrack creates the lead and forwards the browser event id and contact data to the meta tracker', function () {
    $request = consentedRequest();

    $service = Mockery::mock(LeadService::class)->makePartial();
    $service->shouldReceive('trackMetaPixelEvent')
        ->once()
        ->with('Lead', '11111111-2222-4333-8444-555555555555', 'lead@example.com', '+39111222333');
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
    ], $request, '11111111-2222-4333-8444-555555555555');

    expect($lead)->toBeInstanceOf(Lead::class);
    $this->assertDatabaseHas('leads', [
        'name' => 'Tracked Lead',
        'email' => 'lead@example.com',
        'terms' => true,
    ]);
});

test('trackMetaPixelEvent sends to CAPI with enriched UserData using the browser event id', function () {
    $request = consentedRequest();
    $this->app->instance('request', $request);

    MetaPixel::shouldReceive('isEnabled')->once()->andReturn(true);
    MetaPixel::shouldReceive('send')
        ->once()
        ->withArgs(fn (string $eventName, string $eventId, CustomData $custom, UserData $userData): bool => $eventName === 'Lead'
            && $eventId === '11111111-2222-4333-8444-555555555555'
            && $userData instanceof UserData);

    app(LeadService::class)->trackMetaPixelEvent(
        'Lead',
        '11111111-2222-4333-8444-555555555555',
        'lead@example.com',
        '+39111222333',
    );
});

test('trackMetaPixelEvent is a no-op when the pixel is disabled', function () {
    $request = consentedRequest();
    $this->app->instance('request', $request);

    MetaPixel::shouldReceive('isEnabled')->once()->andReturn(false);
    MetaPixel::shouldReceive('send')->never();

    app(LeadService::class)->trackMetaPixelEvent('Lead', '11111111-2222-4333-8444-555555555555');
});

test('trackMetaPixelEvent is a no-op when marketing consent is missing', function () {
    $request = Request::create('https://coine.test/health-check/start', 'POST');
    $this->app->instance('request', $request);

    MetaPixel::shouldReceive('isEnabled')->once()->andReturn(true);
    MetaPixel::shouldReceive('send')->never();

    app(LeadService::class)->trackMetaPixelEvent('startQuiz', '11111111-2222-4333-8444-555555555555');
});

test('trackMetaPixelEvent logs exceptions raised by the SDK', function () {
    $request = consentedRequest();
    $this->app->instance('request', $request);

    MetaPixel::shouldReceive('isEnabled')->once()->andReturn(true);
    MetaPixel::shouldReceive('send')
        ->once()
        ->andThrow(new RuntimeException('meta send failed'));

    Log::shouldReceive('error')
        ->once()
        ->with('meta send failed', Mockery::on(fn (array $context): bool => $context['exception'] === RuntimeException::class));

    app(LeadService::class)->trackMetaPixelEvent('Lead', '11111111-2222-4333-8444-555555555555');
});
