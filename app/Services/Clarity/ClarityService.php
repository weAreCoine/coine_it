<?php

declare(strict_types=1);

namespace App\Services\Clarity;

/**
 * Business-logic layer for Microsoft Clarity tracking
 * (client-side session replay + heatmaps).
 */
class ClarityService
{
    /**
     * Check if Clarity tracking is enabled.
     */
    public static function isEnabled(): bool
    {
        return (bool) config('clarity.enabled')
            && config('clarity.project_id') !== '';
    }

    /**
     * Get the Clarity Project ID.
     */
    public static function projectId(): string
    {
        return (string) config('clarity.project_id');
    }

    /**
     * Whether the verbose client-side console logging is on.
     */
    public static function testEnabled(): bool
    {
        return (bool) config('clarity.test_mode');
    }
}
