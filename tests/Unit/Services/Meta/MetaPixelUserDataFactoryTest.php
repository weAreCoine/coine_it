<?php

use App\Services\Meta\IpGeolocator;
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

test('normalizes first and last names to lowercase and strips non-letter characters', function () {
    $userData = MetaPixelUserDataFactory::make(
        firstName: '  Mario! ',
        lastName: 'De-Luca',
    );

    expect($userData->getFirstName())->toBe('mario')
        ->and($userData->getLastName())->toBe('deluca');
});

test('preserves accented letters and inner whitespace when normalizing names', function () {
    $userData = MetaPixelUserDataFactory::make(
        firstName: 'Niccolò',
        lastName: 'Della Rovere',
    );

    expect($userData->getFirstName())->toBe('niccolò')
        ->and($userData->getLastName())->toBe('della rovere');
});

test('does not set name fields when empty or whitespace-only strings are provided', function () {
    $userData = MetaPixelUserDataFactory::make(firstName: '   ', lastName: '!!!');

    expect($userData->getFirstName())->toBeNull()
        ->and($userData->getLastName())->toBeNull();
});

test('falls back to coine_uid cookie for external_id when no value is passed', function () {
    request()->cookies->set('coine_uid', 'aaaa1111-bbbb-2222-cccc-333344445555');

    $userData = MetaPixelUserDataFactory::make();

    expect($userData->getExternalId())->toBe('aaaa1111-bbbb-2222-cccc-333344445555');
});

test('lets an explicit external_id override the coine_uid cookie', function () {
    request()->cookies->set('coine_uid', 'cookie-value');

    $userData = MetaPixelUserDataFactory::make(externalId: 'explicit-value');

    expect($userData->getExternalId())->toBe('explicit-value');
});

test('does not set external_id when no cookie nor explicit value is available', function () {
    $userData = MetaPixelUserDataFactory::make();

    expect($userData->getExternalId())->toBeNull();
});

test('defaults country to IT when no value is passed', function () {
    $userData = MetaPixelUserDataFactory::make();

    expect($userData->getCountryCode())->toBe('it');
});

test('lets an explicit country override the default', function () {
    $userData = MetaPixelUserDataFactory::make(country: 'FR');

    expect($userData->getCountryCode())->toBe('fr');
});

test('does not set country when an explicit empty string is passed', function () {
    $userData = MetaPixelUserDataFactory::make(country: '');

    expect($userData->getCountryCode())->toBeNull();
});

test('normalizes city to lowercase and collapses inner whitespace', function () {
    $userData = MetaPixelUserDataFactory::make(city: '  Reggio   Emilia ');

    expect($userData->getCity())->toBe('reggioemilia');
});

test('normalizes state to lowercase digit-stripped value', function () {
    $userData = MetaPixelUserDataFactory::make(state: 'RE');

    expect($userData->getState())->toBe('re');
});

test('normalizes zip to digits and letters lowercase without spaces', function () {
    $userData = MetaPixelUserDataFactory::make(zip: '42 121');

    expect($userData->getZipCode())->toBe('42121');
});

test('does not set city, state or zip when whitespace-only is provided', function () {
    $userData = MetaPixelUserDataFactory::make(city: '   ', state: '  ', zip: '  ');

    expect($userData->getCity())->toBeNull()
        ->and($userData->getState())->toBeNull()
        ->and($userData->getZipCode())->toBeNull();
});

test('enriches missing geo fields from the registered IpGeolocator', function () {
    $geolocator = Mockery::mock(IpGeolocator::class);
    $geolocator->shouldReceive('locate')
        ->once()
        ->with('203.0.113.42')
        ->andReturn(['country' => 'FR', 'city' => 'Paris', 'state' => 'IDF', 'zip' => '75001']);

    app()->instance(IpGeolocator::class, $geolocator);

    $userData = MetaPixelUserDataFactory::make();

    expect($userData->getCountryCode())->toBe('fr')
        ->and($userData->getCity())->toBe('paris')
        ->and($userData->getState())->toBe('idf')
        ->and($userData->getZipCode())->toBe('75001');
});

test('explicit caller-supplied geo values take precedence over the IpGeolocator', function () {
    $geolocator = Mockery::mock(IpGeolocator::class);
    $geolocator->shouldReceive('locate')->andReturn([
        'country' => 'FR',
        'city' => 'Paris',
        'state' => 'IDF',
        'zip' => '75001',
    ]);

    app()->instance(IpGeolocator::class, $geolocator);

    $userData = MetaPixelUserDataFactory::make(
        country: 'DE',
        city: 'Berlin',
        state: 'BE',
        zip: '10115',
    );

    expect($userData->getCountryCode())->toBe('de')
        ->and($userData->getCity())->toBe('berlin')
        ->and($userData->getState())->toBe('be')
        ->and($userData->getZipCode())->toBe('10115');
});

test('normalizes Italian phone numbers to digits-only E.164 form', function (string $input, string $expected) {
    $userData = MetaPixelUserDataFactory::make(phone: $input);

    expect($userData->getPhone())->toBe($expected);
})->with([
    'with +39 prefix and separators' => ['+39 333 1234567', '393331234567'],
    'with 00 international prefix' => ['00393331234567', '393331234567'],
    'mobile without country code' => ['3331234567', '393331234567'],
    'landline without country code' => ['0233456789', '390233456789'],
    'already E.164 digits-only' => ['393331234567', '393331234567'],
]);
