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
     * Email, phone, first name and last name are passed unhashed to the
     * Facebook Business SDK, which performs SHA-256 hashing before sending.
     * The external_id falls back to the `coine_uid` cookie set by the
     * EnsureExternalId middleware so the same id is sent on every event for
     * the same visitor across sessions.
     */
    public static function make(
        ?string $email = null,
        ?string $phone = null,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $externalId = null,
    ): UserData {
        $userData = (new UserData)
            ->setClientIpAddress(Request::ip())
            ->setClientUserAgent(Request::userAgent())
            ->setFbp(self::cookie('_fbp'))
            ->setFbc(self::cookie('_fbc'));

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
}
