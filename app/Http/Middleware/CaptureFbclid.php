<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Helpers\CookieConsent;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Captures the `fbclid` URL parameter Meta appends to ad clicks and turns it
 * into a `_fbc` cookie so the server-side Conversions API can attribute the
 * conversion even when the Pixel browser script never runs (ad blockers, race
 * with the consent banner, JS disabled).
 *
 * GDPR posture: `fbclid` is parked in the PHP session — a server-only,
 * first-party necessary storage that lasts only as long as the visit — until
 * the user grants marketing consent. Only then we emit the `_fbc` cookie that
 * Meta expects (TTL 90 days). If the visitor never consents, no marketing
 * cookie is ever written.
 */
class CaptureFbclid
{
    private const COOKIE_NAME = '_fbc';

    private const COOKIE_TTL_MINUTES = 60 * 24 * 90;

    private const SESSION_FBCLID_KEY = 'meta.fbclid';

    private const SESSION_TIMESTAMP_KEY = 'meta.fbclid_ts';

    /**
     * Meta's documented `_fbc` format is `fb.{subdomain_index}.{ts_ms}.{fbclid}`.
     * Subdomain index `1` matches the apex domain (the safest default when in
     * doubt — see https://developers.facebook.com/docs/marketing-api/conversions-api/parameters/fbp-and-fbc).
     */
    private const SUBDOMAIN_INDEX = 1;

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->parkIncomingFbclid($request);

        if (! CookieConsent::hasMarketingConsent()) {
            return $next($request);
        }

        $this->emitFbcCookieWhenMissing($request);

        return $next($request);
    }

    /**
     * Persist the `fbclid` URL parameter (and the moment we observed it) in
     * the PHP session so it survives a delayed consent grant on a later page.
     */
    private function parkIncomingFbclid(Request $request): void
    {
        $fbclid = $request->query('fbclid');

        if (! is_string($fbclid) || $fbclid === '') {
            return;
        }

        $request->session()->put(self::SESSION_FBCLID_KEY, $fbclid);
        $request->session()->put(self::SESSION_TIMESTAMP_KEY, (int) round(microtime(true) * 1000));
    }

    /**
     * Emit the `_fbc` cookie when marketing consent is in place and we don't
     * already have a value to forward to Meta CAPI.
     */
    private function emitFbcCookieWhenMissing(Request $request): void
    {
        $existing = $request->cookie(self::COOKIE_NAME);

        if (is_string($existing) && $existing !== '') {
            return;
        }

        $fbclid = $request->session()->get(self::SESSION_FBCLID_KEY);
        $timestamp = $request->session()->get(self::SESSION_TIMESTAMP_KEY);

        if (! is_string($fbclid) || $fbclid === '') {
            return;
        }

        if (! is_int($timestamp) || $timestamp <= 0) {
            $timestamp = (int) round(microtime(true) * 1000);
        }

        $value = sprintf('fb.%d.%d.%s', self::SUBDOMAIN_INDEX, $timestamp, $fbclid);

        $request->cookies->set(self::COOKIE_NAME, $value);

        Cookie::queue(self::COOKIE_NAME, $value, self::COOKIE_TTL_MINUTES);
    }
}
