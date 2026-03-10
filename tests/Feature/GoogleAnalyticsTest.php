<?php

use App\Services\GoogleAnalytics\GoogleAnalyticsClient;
use App\Services\GoogleAnalytics\GoogleAnalyticsService;
use Br33f\Ga4\MeasurementProtocol\Dto\Response\BaseResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

// --- GoogleAnalyticsService unit tests ---

test('isEnabled returns false when disabled', function () {
    config(['google-analytics.enabled' => false]);

    expect(GoogleAnalyticsService::isEnabled())->toBeFalse();
});

test('isEnabled returns true when properly configured', function () {
    config([
        'google-analytics.enabled' => true,
        'google-analytics.measurement_id' => 'G-TEST123',
        'google-analytics.api_secret' => 'secret',
    ]);

    expect(GoogleAnalyticsService::isEnabled())->toBeTrue();
});

test('isEnabled returns false when measurement_id is empty', function () {
    config([
        'google-analytics.enabled' => true,
        'google-analytics.measurement_id' => '',
        'google-analytics.api_secret' => 'secret',
    ]);

    expect(GoogleAnalyticsService::isEnabled())->toBeFalse();
});

test('extractClientId parses _ga cookie correctly', function () {
    $request = Request::create('/', 'GET', [], ['_ga' => 'GA1.1.123456789.987654321']);

    expect(GoogleAnalyticsService::extractClientId($request))->toBe('123456789.987654321');
});

test('extractClientId returns null without _ga cookie', function () {
    $request = Request::create('/', 'GET');

    expect(GoogleAnalyticsService::extractClientId($request))->toBeNull();
});

test('extractClientId returns null for malformed _ga cookie', function () {
    $request = Request::create('/', 'GET', [], ['_ga' => 'invalid']);

    expect(GoogleAnalyticsService::extractClientId($request))->toBeNull();
});

test('flashEvent stores event in session', function () {
    GoogleAnalyticsService::flashEvent('generate_lead', ['currency' => 'EUR']);

    $events = session()->get('ga4_flash_events');

    expect($events)->toHaveCount(1)
        ->and($events[0]['eventName'])->toBe('generate_lead')
        ->and($events[0]['params'])->toBe(['currency' => 'EUR']);
});

// --- Blade & Inertia integration tests ---

test('gtag script is not rendered without consent', function () {
    config(['google-analytics.enabled' => true, 'google-analytics.measurement_id' => 'G-TEST123']);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertDontSee('googletagmanager.com/gtag', false);
});

test('gtag script is rendered with marketing consent and GA enabled', function () {
    config([
        'google-analytics.enabled' => true,
        'google-analytics.measurement_id' => 'G-TEST123',
        'google-analytics.api_secret' => 'secret',
    ]);

    $response = $this->withUnencryptedCookie('cookie_consent', json_encode(['necessary' => true, 'marketing' => true]))
        ->get(route('home'));

    $response->assertOk();
    $response->assertSee('googletagmanager.com/gtag/js?id=G-TEST123', false);
    $response->assertSee("gtag('config', 'G-TEST123'", false);
});

test('gtag script is not rendered when GA is disabled', function () {
    config(['google-analytics.enabled' => false, 'google-analytics.measurement_id' => 'G-TEST123']);

    $response = $this->withUnencryptedCookie('cookie_consent', json_encode(['necessary' => true, 'marketing' => true]))
        ->get(route('home'));

    $response->assertOk();
    $response->assertDontSee('googletagmanager.com/gtag', false);
});

test('shared props include googleAnalytics data', function () {
    config([
        'google-analytics.enabled' => true,
        'google-analytics.measurement_id' => 'G-TEST123',
        'google-analytics.api_secret' => 'secret',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('googleAnalytics')
            ->where('googleAnalytics.measurementId', 'G-TEST123')
            ->where('googleAnalytics.enabled', true)
            ->where('googleAnalytics.flashEvents', [])
        );
});

test('shared props include GA flash events from session', function () {
    config([
        'google-analytics.enabled' => true,
        'google-analytics.measurement_id' => 'G-TEST123',
        'google-analytics.api_secret' => 'secret',
    ]);

    $this->withSession(['ga4_flash_events' => [
        ['eventName' => 'generate_lead', 'params' => []],
    ]])
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('googleAnalytics.flashEvents', [
                ['eventName' => 'generate_lead', 'params' => []],
            ])
        );
});

// --- Server-side tracking tests ---

test('trackPageView sends event when consent and _ga cookie are present', function () {
    config([
        'google-analytics.enabled' => true,
        'google-analytics.measurement_id' => 'G-TEST123',
        'google-analytics.api_secret' => 'secret',
    ]);

    $mockClient = Mockery::mock(GoogleAnalyticsClient::class);
    $mockClient->shouldReceive('sendEvent')
        ->once()
        ->with('123456789.987654321', 'page_view', Mockery::type('array'))
        ->andReturn(new BaseResponse(new GuzzleHttp\Psr7\Response(204)));

    $this->app->instance(GoogleAnalyticsClient::class, $mockClient);

    $this->withUnencryptedCookie('cookie_consent', json_encode(['necessary' => true, 'marketing' => true]))
        ->withUnencryptedCookie('_ga', 'GA1.1.123456789.987654321')
        ->get(route('home'))
        ->assertOk();
});

test('trackPageView does not send event without marketing consent', function () {
    config([
        'google-analytics.enabled' => true,
        'google-analytics.measurement_id' => 'G-TEST123',
        'google-analytics.api_secret' => 'secret',
    ]);

    $mockClient = Mockery::mock(GoogleAnalyticsClient::class);
    $mockClient->shouldNotReceive('sendEvent');

    $this->app->instance(GoogleAnalyticsClient::class, $mockClient);

    $this->withUnencryptedCookie('_ga', 'GA1.1.123456789.987654321')
        ->get(route('home'))
        ->assertOk();
});

test('trackPageView does not send event without _ga cookie', function () {
    config([
        'google-analytics.enabled' => true,
        'google-analytics.measurement_id' => 'G-TEST123',
        'google-analytics.api_secret' => 'secret',
    ]);

    $mockClient = Mockery::mock(GoogleAnalyticsClient::class);
    $mockClient->shouldNotReceive('sendEvent');

    $this->app->instance(GoogleAnalyticsClient::class, $mockClient);

    $this->withUnencryptedCookie('cookie_consent', json_encode(['necessary' => true, 'marketing' => true]))
        ->get(route('home'))
        ->assertOk();
});

test('contact form tracks generate_lead via GA4 server-side', function () {
    Mail::fake();

    config([
        'google-analytics.enabled' => true,
        'google-analytics.measurement_id' => 'G-TEST123',
        'google-analytics.api_secret' => 'secret',
    ]);

    $mockClient = Mockery::mock(GoogleAnalyticsClient::class);
    $mockClient->shouldReceive('sendEvent')
        ->once()
        ->with('123456789.987654321', 'generate_lead', [])
        ->andReturn(new BaseResponse(new GuzzleHttp\Psr7\Response(204)));

    // Also allow page_view from middleware
    $mockClient->shouldReceive('sendEvent')
        ->with('123456789.987654321', 'page_view', Mockery::type('array'))
        ->andReturn(new BaseResponse(new GuzzleHttp\Psr7\Response(204)));

    $this->app->instance(GoogleAnalyticsClient::class, $mockClient);

    $this->withUnencryptedCookie('cookie_consent', json_encode(['necessary' => true, 'marketing' => true]))
        ->withUnencryptedCookie('_ga', 'GA1.1.123456789.987654321')
        ->post(route('contact.store'), [
            'firstName' => 'Test',
            'lastName' => 'User',
            'email' => 'ga4test@example.com',
            'phone' => null,
            'message' => 'GA4 test project',
            'termsAccepted' => true,
        ])->assertOk();

    $this->assertDatabaseHas('leads', ['email' => 'ga4test@example.com']);
});
