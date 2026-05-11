<?php

use App\Http\Middleware\TrackMetaPageView;
use App\Jobs\SendMetaConversionEventJob;
use App\Models\User;
use Combindma\FacebookPixel\Facades\MetaPixel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

function metaTrackingRequest(string $url): Request
{
    return Request::create($url, 'GET', [], [
        'cookie_consent' => json_encode(['necessary' => true, 'marketing' => true]),
    ]);
}

test('track meta page view middleware sends a page view when consent and event id are present', function () {
    Bus::fake();

    $request = metaTrackingRequest('https://coine.test/contact');
    $request->attributes->set('meta_pixel_event_id', 'evt-123');
    $this->app->instance('request', $request);

    MetaPixel::shouldReceive('isEnabled')->once()->andReturn(true);

    $response = new Response('ok');

    $result = app(TrackMetaPageView::class)->handle($request, fn () => $response);

    expect($result)->toBe($response);

    Bus::assertDispatched(
        SendMetaConversionEventJob::class,
        fn (SendMetaConversionEventJob $job) => $job->eventName === 'PageView' && $job->eventId === 'evt-123',
    );
});

test('track meta page view middleware skips api requests', function () {
    Bus::fake();

    $request = metaTrackingRequest('https://coine.test/api/webhooks/calendly');
    $request->attributes->set('meta_pixel_event_id', 'evt-123');
    $this->app->instance('request', $request);

    MetaPixel::shouldReceive('isEnabled')->once()->andReturn(true);

    $response = new Response('ok');

    $result = app(TrackMetaPageView::class)->handle($request, fn () => $response);

    expect($result)->toBe($response);
    Bus::assertNotDispatched(SendMetaConversionEventJob::class);
});

test('track meta page view middleware skips requests without an event id', function () {
    Bus::fake();

    $request = metaTrackingRequest('https://coine.test/contact');
    $this->app->instance('request', $request);

    MetaPixel::shouldReceive('isEnabled')->once()->andReturn(true);

    $response = new Response('ok');

    $result = app(TrackMetaPageView::class)->handle($request, fn () => $response);

    expect($result)->toBe($response);
    Bus::assertNotDispatched(SendMetaConversionEventJob::class);
});

test('track meta page view middleware enriches user_data with the authenticated user info', function () {
    Bus::fake();

    $request = metaTrackingRequest('https://coine.test/dashboard');
    $request->attributes->set('meta_pixel_event_id', 'evt-auth');

    $user = User::factory()->create([
        'name' => 'Mario Rossi',
        'email' => 'Mario.Rossi@Example.com',
    ]);

    $this->actingAs($user);
    $this->app->instance('request', $request);

    MetaPixel::shouldReceive('isEnabled')->once()->andReturn(true);

    $response = new Response('ok');

    $result = app(TrackMetaPageView::class)->handle($request, fn () => $response);

    expect($result)->toBe($response);

    Bus::assertDispatched(
        SendMetaConversionEventJob::class,
        function (SendMetaConversionEventJob $job) {
            return $job->eventName === 'PageView'
                && $job->eventId === 'evt-auth'
                && ($job->userDataAttributes['email'] ?? null) === 'mario.rossi@example.com'
                && ($job->userDataAttributes['firstName'] ?? null) === 'mario'
                && ($job->userDataAttributes['lastName'] ?? null) === 'rossi'
                && ($job->userDataAttributes['country'] ?? null) === 'it';
        },
    );
});

test('track meta page view middleware logs and swallows send failures', function () {
    config()->set('meta-pixel.queue_enabled', false);

    $request = metaTrackingRequest('https://coine.test/contact');
    $request->attributes->set('meta_pixel_event_id', 'evt-999');
    $this->app->instance('request', $request);

    MetaPixel::shouldReceive('isEnabled')->once()->andReturn(true);
    MetaPixel::shouldReceive('send')
        ->once()
        ->andThrow(new RuntimeException('meta failed'));

    Log::shouldReceive('channel')->with('meta-capi')->andReturn(
        tap(Mockery::mock(\Psr\Log\LoggerInterface::class), fn ($m) => $m->shouldReceive('error'))
    );

    Log::shouldReceive('error')
        ->once()
        ->with('Meta Pixel CAPI PageView failed', ['error' => 'meta failed']);

    $response = new Response('ok');

    $result = app(TrackMetaPageView::class)->handle($request, fn () => $response);

    expect($result)->toBe($response);
});
