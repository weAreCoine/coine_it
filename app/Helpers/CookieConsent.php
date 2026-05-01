<?php

declare(strict_types=1);

namespace App\Helpers;

class CookieConsent
{
    /**
     * Check if the user has given marketing consent.
     */
    public static function hasMarketingConsent(): bool
    {
        if (self::hasLocalForceOverride()) {
            return true;
        }

        $consent = self::getConsent();

        return $consent['marketing'] ?? false;
    }

    /**
     * Check if the user has given analytics consent.
     */
    public static function hasAnalyticsConsent(): bool
    {
        if (self::hasLocalForceOverride()) {
            return true;
        }

        $consent = self::getConsent();

        return $consent['analytics'] ?? false;
    }

    /**
     * Check if the user has made a consent choice.
     */
    public static function hasConsent(): bool
    {
        return request()->cookie('cookie_consent') !== null;
    }

    /**
     * Get the decoded consent array.
     *
     * @return array{necessary: bool, marketing: bool, analytics: bool}
     */
    public static function getConsent(): array
    {
        $raw = request()->cookie('cookie_consent');

        if ($raw === null) {
            return ['necessary' => true, 'marketing' => false, 'analytics' => false];
        }

        $decoded = json_decode($raw, true);

        if (! is_array($decoded)) {
            return ['necessary' => true, 'marketing' => false, 'analytics' => false];
        }

        return [
            'necessary' => true,
            'marketing' => (bool) ($decoded['marketing'] ?? false),
            'analytics' => (bool) ($decoded['analytics'] ?? false),
        ];
    }

    /**
     * Local-environment debug override that forces consent to true when
     * the request carries `?force_consent=1`.
     */
    private static function hasLocalForceOverride(): bool
    {
        return app()->environment('local') && request()->query('force_consent') === '1';
    }
}
