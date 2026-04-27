<?php

use App\Services\Meta\MetaPixelUserDataFactory;
use FacebookAds\Object\ServerSide\UserData;
use Illuminate\Http\Request;

uses(Tests\TestCase::class);

beforeEach(function () {
    $request = Request::create('https://coine.test/health-check', 'GET', server: [
        'REMOTE_ADDR' => '203.0.113.42',
        'HTTP_USER_AGENT' => 'Mozilla/5.0 (Pest)',
    ]);

    $request->cookies->set('_fbp', 'fb.1.123.abc');
    $request->cookies->set('_fbc', 'fb.1.456.def');

    $this->app->instance('request', $request);
});

test('builds UserData with cookies, ip and user agent when no contact info is provided', function () {
    $userData = MetaPixelUserDataFactory::make();

    expect($userData)->toBeInstanceOf(UserData::class)
        ->and($userData->getFbp())->toBe('fb.1.123.abc')
        ->and($userData->getFbc())->toBe('fb.1.456.def')
        ->and($userData->getClientIpAddress())->toBe('203.0.113.42')
        ->and($userData->getClientUserAgent())->toBe('Mozilla/5.0 (Pest)');
});

test('lowercases email and strips non-digits from phone before handing to the Facebook SDK', function () {
    $userData = MetaPixelUserDataFactory::make('Lead@Example.com', '+39 (011) 555-12-34');

    // The Facebook Business SDK takes raw values and normalizes + hashes them
    // during serialization, so we just need to make sure we feed it normalized inputs.
    expect($userData->getEmail())->toBe('lead@example.com')
        ->and($userData->getPhone())->toBe('390115551234');
});

test('does not set email or phone when empty strings are provided', function () {
    $userData = MetaPixelUserDataFactory::make('', '');

    expect($userData->getEmail())->toBeNull()
        ->and($userData->getPhone())->toBeNull();
});
