<?php

use App\Http\Middleware\TrackMetaPageView;
use Combindma\FacebookPixel\Facades\MetaPixel;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\UserData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

function metaTrackingRequest(string $url): Request
{
    return Request::create($url, 'GET', [], [
        'cookie_consent' => json_encode(['necessary' => true, 'marketing' => true]),
    ]);
}

test('track meta page view middleware sends a page view when consent and event id are present', function () {
    $request = metaTrackingRequest('https://coine.test/contact');
    $request->attributes->set('meta_pixel_event_id', 'evt-123');
    $this->app->instance('request', $request);

    MetaPixel::shouldReceive('isEnabled')->once()->andReturn(true);
    MetaPixel::shouldReceive('send')
        ->once()
        ->with('PageView', 'evt-123', Mockery::type(CustomData::class), Mockery::type(UserData::class));

    $response = new Response('ok');

    $result = app(TrackMetaPageView::class)->handle($request, fn () => $response);

    expect($result)->toBe($response);
});

test('track meta page view middleware skips api requests', function () {
    $request = metaTrackingRequest('https://coine.test/api/webhooks/calendly');
    $request->attributes->set('meta_pixel_event_id', 'evt-123');
    $this->app->instance('request', $request);

    MetaPixel::shouldReceive('isEnabled')->once()->andReturn(true);
    MetaPixel::shouldReceive('send')->never();

    $response = new Response('ok');

    $result = app(TrackMetaPageView::class)->handle($request, fn () => $response);

    expect($result)->toBe($response);
});

test('track meta page view middleware skips requests without an event id', function () {
    $request = metaTrackingRequest('https://coine.test/contact');
    $this->app->instance('request', $request);

    MetaPixel::shouldReceive('isEnabled')->once()->andReturn(true);
    MetaPixel::shouldReceive('send')->never();

    $response = new Response('ok');

    $result = app(TrackMetaPageView::class)->handle($request, fn () => $response);

    expect($result)->toBe($response);
});

test('track meta page view middleware logs and swallows send failures', function () {
    $request = metaTrackingRequest('https://coine.test/contact');
    $request->attributes->set('meta_pixel_event_id', 'evt-999');
    $this->app->instance('request', $request);

    MetaPixel::shouldReceive('isEnabled')->once()->andReturn(true);
    MetaPixel::shouldReceive('send')
        ->once()
        ->andThrow(new RuntimeException('meta failed'));

    Log::shouldReceive('error')
        ->once()
        ->with('Meta Pixel CAPI PageView failed', ['error' => 'meta failed']);

    $response = new Response('ok');

    $result = app(TrackMetaPageView::class)->handle($request, fn () => $response);

    expect($result)->toBe($response);
});
