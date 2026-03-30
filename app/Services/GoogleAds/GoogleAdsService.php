<?php

declare(strict_types=1);

namespace App\Services\GoogleAds;

/**
 * Configuration helper for Google Ads (gtag.js) tracking.
 */
class GoogleAdsService
{
    /**
     * Check if Google Ads tracking is enabled.
     */
    public static function isEnabled(): bool
    {
        return (bool) config('google-ads.enabled')
            && config('google-ads.conversion_id') !== '';
    }

    /**
     * Get the Google Ads Conversion ID.
     */
    public static function conversionId(): string
    {
        return (string) config('google-ads.conversion_id');
    }
}
