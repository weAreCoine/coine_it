<?php

declare(strict_types=1);

namespace App\Services\Meta;

use FacebookAds\Object\ServerSide\UserData;
use Illuminate\Support\Facades\Request;

class MetaPixelUserDataFactory
{
    /**
     * Build a UserData payload for the Meta Conversions API.
     *
     * Always includes _fbp / _fbc cookies, client IP and user agent so that
     * Meta can match the server event to the matching browser pixel event.
     * Email, phone, first name, last name and the geo parameters are passed
     * unhashed to the Facebook Business SDK, which performs SHA-256 hashing
     * before sending. The external_id falls back to the `coine_uid` cookie
     * set by the EnsureExternalId middleware so the same id is sent on every
     * event for the same visitor across sessions.
     *
     * `country` defaults to "it" to give the SDK a meaningful value even when
     * we don't know the visitor's exact location — passing an explicit empty
     * string disables the default for cases where we genuinely don't know.
     */
    public static function make(
        ?string $email = null,
        ?string $phone = null,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $externalId = null,
        ?string $country = 'it',
        ?string $city = null,
        ?string $state = null,
        ?string $zip = null,
    ): UserData {
        $clientIp = Request::ip();

        $userData = (new UserData)
            ->setClientIpAddress($clientIp)
            ->setClientUserAgent(Request::userAgent())
            ->setFbp(self::cookie('_fbp'))
            ->setFbc(self::cookie('_fbc'));

        [$country, $city, $state, $zip] = self::enrichWithGeo($clientIp, $country, $city, $state, $zip);

        if ($email !== null && $email !== '') {
            $userData->setEmail(strtolower(trim($email)));
        }

        if ($phone !== null && $phone !== '') {
            $userData->setPhone(self::normalizePhone($phone));
        }

        if ($firstName !== null && $firstName !== '') {
            $normalized = self::normalizeName($firstName);

            if ($normalized !== '') {
                $userData->setFirstName($normalized);
            }
        }

        if ($lastName !== null && $lastName !== '') {
            $normalized = self::normalizeName($lastName);

            if ($normalized !== '') {
                $userData->setLastName($normalized);
            }
        }

        $resolvedExternalId = $externalId ?? self::cookie('coine_uid');

        if ($resolvedExternalId !== null && $resolvedExternalId !== '') {
            $userData->setExternalId($resolvedExternalId);
        }

        if ($country !== null && $country !== '') {
            $normalized = self::normalizeCountry($country);

            if ($normalized !== '') {
                $userData->setCountryCode($normalized);
            }
        }

        if ($city !== null && $city !== '') {
            $normalized = self::normalizeCity($city);

            if ($normalized !== '') {
                $userData->setCity($normalized);
            }
        }

        if ($state !== null && $state !== '') {
            $normalized = self::normalizeState($state);

            if ($normalized !== '') {
                $userData->setState($normalized);
            }
        }

        if ($zip !== null && $zip !== '') {
            $normalized = self::normalizeZip($zip);

            if ($normalized !== '') {
                $userData->setZipCode($normalized);
            }
        }

        return $userData;
    }

    private static function cookie(string $name): ?string
    {
        $value = Request::cookie($name);

        return is_string($value) && $value !== '' ? $value : null;
    }

    /**
     * Normalize a phone number to digits-only E.164 form, applying Italian
     * defaults when the country prefix is missing. The Facebook Business SDK
     * still applies SHA-256 hashing before sending the value.
     */
    private static function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone) ?? '';

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '39')) {
            return $digits;
        }

        $length = strlen($digits);

        if ($length >= 9 && $length <= 10) {
            return '39'.$digits;
        }

        return $digits;
    }

    /**
     * Normalize a personal name by trimming, lowercasing and stripping any
     * character that is not a letter (Unicode-aware) or whitespace. Mirrors
     * the normalization the Pixel browser applies before hashing so the
     * server-side hash matches.
     */
    private static function normalizeName(string $name): string
    {
        $trimmed = trim($name);

        if ($trimmed === '') {
            return '';
        }

        $stripped = preg_replace('/[^\p{L}\s]+/u', '', $trimmed) ?? '';
        $collapsed = preg_replace('/\s+/u', ' ', $stripped) ?? '';

        return mb_strtolower(trim($collapsed));
    }

    /**
     * Normalize a country code to the lowercase ISO 3166-1 alpha-2 form
     * expected by Meta (e.g. "IT" → "it"). Anything longer than 2 chars is
     * truncated to keep the SDK happy when callers pass full country names.
     */
    private static function normalizeCountry(string $country): string
    {
        $stripped = preg_replace('/[^A-Za-z]/', '', $country) ?? '';

        if ($stripped === '') {
            return '';
        }

        return mb_strtolower(mb_substr($stripped, 0, 2));
    }

    /**
     * Normalize a city to lowercase letters only, stripping spaces and
     * punctuation. Meta hashes the result, so removing whitespace prevents
     * "Reggio Emilia" and "ReggioEmilia" from producing different hashes.
     */
    private static function normalizeCity(string $city): string
    {
        $stripped = preg_replace('/[^\p{L}]+/u', '', $city) ?? '';

        return mb_strtolower($stripped);
    }

    /**
     * Normalize a state to a lowercase short code (max 5 chars to absorb
     * non-Italian variants), stripping anything that is not a letter.
     */
    private static function normalizeState(string $state): string
    {
        $stripped = preg_replace('/[^\p{L}]+/u', '', $state) ?? '';

        if ($stripped === '') {
            return '';
        }

        return mb_strtolower(mb_substr($stripped, 0, 5));
    }

    /**
     * Normalize a postal code by stripping whitespace and lowercasing, keeping
     * digits and letters (UK-style codes contain both).
     */
    private static function normalizeZip(string $zip): string
    {
        $stripped = preg_replace('/[^A-Za-z0-9]/', '', $zip) ?? '';

        return mb_strtolower($stripped);
    }

    /**
     * Resolve the client IP through the registered IpGeolocator and fold the
     * lookup into the caller-supplied geo parameters. The caller's explicit
     * values always win: geo lookup only fills the gaps. The country default
     * `'it'` is treated as a fallback too — a non-null geo `country` will
     * override it because the lookup is more specific than the static guess.
     *
     * @return array{0: ?string, 1: ?string, 2: ?string, 3: ?string}
     */
    private static function enrichWithGeo(
        ?string $clientIp,
        ?string $country,
        ?string $city,
        ?string $state,
        ?string $zip,
    ): array {
        $shouldLookup = $clientIp !== null && $clientIp !== '' && (
            ($country === null || $country === 'it')
            || $city === null
            || $state === null
            || $zip === null
        );

        if (! $shouldLookup) {
            return [$country, $city, $state, $zip];
        }

        try {
            $geo = app(IpGeolocator::class)->locate($clientIp);
        } catch (\Throwable) {
            return [$country, $city, $state, $zip];
        }

        return [
            $country === null || $country === 'it' ? ($geo['country'] ?? $country) : $country,
            $city ?? $geo['city'],
            $state ?? $geo['state'],
            $zip ?? $geo['zip'],
        ];
    }
}
