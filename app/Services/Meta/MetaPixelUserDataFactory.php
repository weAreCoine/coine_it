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
     * When email or phone are provided they are passed unhashed to the
     * Facebook Business SDK, which performs SHA-256 hashing before sending.
     */
    public static function make(?string $email = null, ?string $phone = null): UserData
    {
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

        return $userData;
    }

    private static function cookie(string $name): ?string
    {
        $value = Request::cookie($name);

        return is_string($value) && $value !== '' ? $value : null;
    }

    private static function normalizePhone(string $phone): string
    {
        return preg_replace('/\D/', '', $phone) ?? '';
    }
}
