<?php

declare(strict_types=1);

namespace App\Services\Meta;

/**
 * @phpstan-type GeoLookup array{country: ?string, city: ?string, state: ?string, zip: ?string}
 */
interface IpGeolocator
{
    /**
     * Resolve a client IP to a geolocation tuple. Implementations should return
     * `['country' => null, 'city' => null, 'state' => null, 'zip' => null]`
     * when the lookup fails, rather than throwing — geo enrichment is
     * best-effort and must never block the CAPI send.
     *
     * @return array{country: ?string, city: ?string, state: ?string, zip: ?string}
     */
    public function locate(string $ip): array;
}
