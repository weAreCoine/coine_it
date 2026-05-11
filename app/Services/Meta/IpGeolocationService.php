<?php

declare(strict_types=1);

namespace App\Services\Meta;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;
use Torann\GeoIP\GeoIP;

/**
 * Resolves a client IP into a geolocation tuple via Torann\GeoIP (backed by
 * the local MaxMind GeoLite2 database). Results are cached per IP for 24h so
 * the same visitor browsing many pages doesn't hammer the lookup.
 *
 * Failures degrade silently to an empty tuple — geo data is a nice-to-have
 * for EMQ, never a hard requirement, and we don't want a missing or
 * corrupted DB to block the response cycle.
 */
class IpGeolocationService implements IpGeolocator
{
    private const CACHE_TTL_SECONDS = 86400;

    public function __construct(private readonly GeoIP $geoip) {}

    public function locate(string $ip): array
    {
        if ($ip === '') {
            return self::empty();
        }

        return Cache::remember(
            'meta-geoip:'.$ip,
            self::CACHE_TTL_SECONDS,
            fn (): array => $this->resolve($ip),
        );
    }

    /**
     * @return array{country: ?string, city: ?string, state: ?string, zip: ?string}
     */
    private function resolve(string $ip): array
    {
        try {
            $location = $this->geoip->getLocation($ip);
        } catch (Throwable $e) {
            Log::channel('meta-capi')->warning('GeoIP lookup failed', [
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);

            return self::empty();
        }

        if ($location->default ?? false) {
            return self::empty();
        }

        return [
            'country' => self::stringOrNull($location->iso_code),
            'city' => self::stringOrNull($location->city),
            'state' => self::stringOrNull($location->state),
            'zip' => self::stringOrNull($location->postal_code),
        ];
    }

    /**
     * @return array{country: ?string, city: ?string, state: ?string, zip: ?string}
     */
    private static function empty(): array
    {
        return ['country' => null, 'city' => null, 'state' => null, 'zip' => null];
    }

    private static function stringOrNull(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
