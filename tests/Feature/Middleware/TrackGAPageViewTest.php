<?php

use App\Http\Middleware\TrackGAPageView;
use App\Services\GoogleAnalytics\GoogleAnalyticsClient;
use Br33f\Ga4\MeasurementProtocol\Dto\Response\BaseResponse;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

function trackingRequest(string $url): Request
{
    return Request::create($url, 'GET', [], [
        'cookie_consent' => json_encode(['necessary' => true, 'marketing' => true]),
        '_ga' => 'GA1.1.123456789.987654321',
    ]);
}

test('track ga page view middleware sends a page view for non-api requests', function () {
    config([
        'google-analytics.enabled' => true,
        'google-analytics.measurement_id' => 'G-TEST123',
        'google-analytics.api_secret' => 'secret',
    ]);

    $request = trackingRequest('https://coine.test/pricing?plan=pro');
    $this->app->instance('request', $request);

    $client = Mockery::mock(GoogleAnalyticsClient::class);
    $client->shouldReceive('sendEvent')
        ->once()
        ->with('123456789.987654321', 'page_view', [
            'page_location' => 'https://coine.test/pricing?plan=pro',
            'page_title' => '',
        ])
        ->andReturn(new BaseResponse(new Psr7Response(204)));

    $this->app->instance(GoogleAnalyticsClient::class, $client);

    $response = new Response('ok');

    $result = app(TrackGAPageView::class)->handle($request, fn () => $response);

    expect($result)->toBe($response);
});

test('track ga page view middleware skips api requests', function () {
    config([
        'google-analytics.enabled' => true,
        'google-analytics.measurement_id' => 'G-TEST123',
        'google-analytics.api_secret' => 'secret',
    ]);

    $request = trackingRequest('https://coine.test/api/webhooks/calendly');
    $this->app->instance('request', $request);

    $client = Mockery::mock(GoogleAnalyticsClient::class);
    $client->shouldNotReceive('sendEvent');

    $this->app->instance(GoogleAnalyticsClient::class, $client);

    $response = new Response('ok');

    $result = app(TrackGAPageView::class)->handle($request, fn () => $response);

    expect($result)->toBe($response);
});
