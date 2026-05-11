<?php

use App\Services\Meta\IpGeolocationService;
use Illuminate\Support\Facades\Cache;
use Torann\GeoIP\GeoIP;
use Torann\GeoIP\Location;

uses(Tests\TestCase::class);

beforeEach(function () {
    Cache::flush();
});

test('returns the geo tuple from the GeoIP service when the lookup succeeds', function () {
    $geoip = Mockery::mock(GeoIP::class);
    $geoip->shouldReceive('getLocation')
        ->once()
        ->with('203.0.113.42')
        ->andReturn(new Location([
            'iso_code' => 'IT',
            'city' => 'Reggio Emilia',
            'state' => 'RE',
            'postal_code' => '42121',
            'default' => false,
        ]));

    $result = (new IpGeolocationService($geoip))->locate('203.0.113.42');

    expect($result)->toBe([
        'country' => 'IT',
        'city' => 'Reggio Emilia',
        'state' => 'RE',
        'zip' => '42121',
    ]);
});

test('returns an empty tuple when the GeoIP service returns the default location', function () {
    $geoip = Mockery::mock(GeoIP::class);
    $geoip->shouldReceive('getLocation')
        ->andReturn(new Location([
            'iso_code' => 'US',
            'city' => 'Unknown',
            'state' => 'NA',
            'postal_code' => '00000',
            'default' => true,
        ]));

    $result = (new IpGeolocationService($geoip))->locate('203.0.113.42');

    expect($result)->toBe([
        'country' => null,
        'city' => null,
        'state' => null,
        'zip' => null,
    ]);
});

test('returns an empty tuple when the GeoIP service throws', function () {
    $geoip = Mockery::mock(GeoIP::class);
    $geoip->shouldReceive('getLocation')->andThrow(new RuntimeException('db missing'));

    $result = (new IpGeolocationService($geoip))->locate('203.0.113.42');

    expect($result)->toBe([
        'country' => null,
        'city' => null,
        'state' => null,
        'zip' => null,
    ]);
});

test('caches the lookup result per IP for 24 hours', function () {
    $geoip = Mockery::mock(GeoIP::class);
    $geoip->shouldReceive('getLocation')
        ->once()
        ->andReturn(new Location([
            'iso_code' => 'IT',
            'city' => 'Milano',
            'state' => 'MI',
            'postal_code' => '20100',
            'default' => false,
        ]));

    $service = new IpGeolocationService($geoip);

    $first = $service->locate('203.0.113.42');
    $second = $service->locate('203.0.113.42');

    expect($second)->toBe($first);
});

test('returns an empty tuple when the IP is blank', function () {
    $geoip = Mockery::mock(GeoIP::class);
    $geoip->shouldNotReceive('getLocation');

    $result = (new IpGeolocationService($geoip))->locate('');

    expect($result)->toBe([
        'country' => null,
        'city' => null,
        'state' => null,
        'zip' => null,
    ]);
});
