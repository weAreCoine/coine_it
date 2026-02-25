<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Services\Iubenda;
use Illuminate\Support\Collection;

class IubendaHelper extends Iubenda
{
    public static function hasMarketingConsent(): bool
    {
        return ! self::bot_detected() && self::consentStatus()->get('marketing', false);
    }

    public static function consentStatus(): Collection
    {
        static $consentStatus = null;
        if ($consentStatus === null) {
            foreach (self::get_purposes() as $purpose => $status) {
                $consentStatus[match ($purpose) {
                    1 => 'necessary',
                    2 => 'functionality',
                    3 => 'experience',
                    4 => 'measurement',
                    5 => 'marketing'
                }] = $status;
            }
        }

        return collect($consentStatus);
    }

    public static function hasMeasurementConsent(): bool
    {
        return ! self::bot_detected() && self::consentStatus()->get('measurement', false);
    }

    public static function hasExperienceConsent(): bool
    {
        return ! self::bot_detected() && self::consentStatus()->get('experience', false);
    }

    public static function hasFunctionalityConsent(): bool
    {
        return ! self::bot_detected() && self::consentStatus()->get('functionality', false);
    }
}
