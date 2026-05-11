<?php

use App\Jobs\SendMetaConversionEventJob;
use App\Models\Lead;
use App\Services\LeadService;
use Combindma\FacebookPixel\Facades\MetaPixel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;

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
        ->with(
            'Lead',
            '11111111-2222-4333-8444-555555555555',
            'lead@example.com',
            '+39111222333',
            'Tracked',
            'Lead',
            'https://coine.test/contact',
        );
    $service->shouldReceive('trackGAEvent')
        ->once()
        ->with($request, 'generate_lead');
    $service->shouldReceive('trackLinkedInEvent')
        ->once()
        ->with($request, 'lead', 'lead@example.com');

    $lead = $service->createAndTrack([
        'first_name' => 'Tracked',
        'last_name' => 'Lead',
        'name' => 'Tracked Lead',
        'email' => 'lead@example.com',
        'phone' => '+39111222333',
        'terms' => true,
    ], $request, '11111111-2222-4333-8444-555555555555');

    expect($lead)->toBeInstanceOf(Lead::class);
    $this->assertDatabaseHas('leads', [
        'first_name' => 'Tracked',
        'last_name' => 'Lead',
        'name' => 'Tracked Lead',
        'email' => 'lead@example.com',
        'terms' => true,
    ]);
});

test('trackMetaPixelEvent dispatches a queue job with the enriched UserData and browser event id', function () {
    Bus::fake();
    $request = consentedRequest();
    $this->app->instance('request', $request);

    MetaPixel::shouldReceive('isEnabled')->once()->andReturn(true);

    app(LeadService::class)->trackMetaPixelEvent(
        'Lead',
        '11111111-2222-4333-8444-555555555555',
        'lead@example.com',
        '+39111222333',
        eventSourceUrl: 'https://coine.test/contattaci',
    );

    Bus::assertDispatched(
        SendMetaConversionEventJob::class,
        function (SendMetaConversionEventJob $job) {
            return $job->eventName === 'Lead'
                && $job->eventId === '11111111-2222-4333-8444-555555555555'
                && ($job->userDataAttributes['email'] ?? null) === 'lead@example.com'
                && ($job->userDataAttributes['phone'] ?? null) === '39111222333'
                && $job->eventSourceUrl === 'https://coine.test/contattaci';
        },
    );
});

test('trackMetaPixelEvent falls back to the referer URL when no explicit URL is supplied', function () {
    Bus::fake();
    $request = Request::create('https://coine.test/contact', 'POST', [], [
        'cookie_consent' => json_encode(['necessary' => true, 'marketing' => true]),
    ], [], ['HTTP_REFERER' => 'https://coine.test/contattaci?utm=foo']);
    $this->app->instance('request', $request);

    MetaPixel::shouldReceive('isEnabled')->once()->andReturn(true);

    app(LeadService::class)->trackMetaPixelEvent(
        'Lead',
        '11111111-2222-4333-8444-555555555555',
    );

    Bus::assertDispatched(
        SendMetaConversionEventJob::class,
        fn (SendMetaConversionEventJob $job) => $job->eventSourceUrl === 'https://coine.test/contattaci?utm=foo',
    );
});

test('trackMetaPixelEvent ignores referer from a different host', function () {
    Bus::fake();
    $request = Request::create('https://coine.test/contact', 'POST', [], [
        'cookie_consent' => json_encode(['necessary' => true, 'marketing' => true]),
    ], [], ['HTTP_REFERER' => 'https://attacker.example/foo']);
    $this->app->instance('request', $request);

    MetaPixel::shouldReceive('isEnabled')->once()->andReturn(true);

    app(LeadService::class)->trackMetaPixelEvent(
        'Lead',
        '11111111-2222-4333-8444-555555555555',
    );

    Bus::assertDispatched(
        SendMetaConversionEventJob::class,
        fn (SendMetaConversionEventJob $job) => $job->eventSourceUrl === 'https://coine.test/contact',
    );
});

test('trackMetaPixelEvent is a no-op when the pixel is disabled', function () {
    Bus::fake();
    $request = consentedRequest();
    $this->app->instance('request', $request);

    MetaPixel::shouldReceive('isEnabled')->once()->andReturn(false);

    app(LeadService::class)->trackMetaPixelEvent('Lead', '11111111-2222-4333-8444-555555555555');

    Bus::assertNotDispatched(SendMetaConversionEventJob::class);
});

test('trackMetaPixelEvent is a no-op when marketing consent is missing', function () {
    Bus::fake();
    $request = Request::create('https://coine.test/health-check/start', 'POST');
    $this->app->instance('request', $request);

    MetaPixel::shouldReceive('isEnabled')->once()->andReturn(true);

    app(LeadService::class)->trackMetaPixelEvent('startQuiz', '11111111-2222-4333-8444-555555555555');

    Bus::assertNotDispatched(SendMetaConversionEventJob::class);
});
