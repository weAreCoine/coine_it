<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensures a stable first-party visitor identifier is set as the `coine_uid`
 * cookie. The value is forwarded to Meta CAPI as `external_id` (and to the
 * Pixel browser via Inertia shared props) to lift the Event Match Quality
 * score across cross-session conversions.
 */
class EnsureExternalId
{
    private const COOKIE_NAME = 'coine_uid';

    private const TWO_YEARS_IN_MINUTES = 60 * 24 * 365 * 2;

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $existing = $request->cookie(self::COOKIE_NAME);

        if (is_string($existing) && $existing !== '') {
            return $next($request);
        }

        $uuid = (string) Str::uuid();

        $request->cookies->set(self::COOKIE_NAME, $uuid);

        Cookie::queue(
            self::COOKIE_NAME,
            $uuid,
            self::TWO_YEARS_IN_MINUTES,
        );

        return $next($request);
    }
}
