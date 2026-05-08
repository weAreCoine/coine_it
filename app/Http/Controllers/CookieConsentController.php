<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CookieConsentRequest;
use App\Models\CookieConsent;
use Illuminate\Http\Response;

class CookieConsentController extends Controller
{
    /**
     * Persist (or refresh) the cookie consent choice for the current visitor.
     *
     * The endpoint is idempotent on `consent_id`: re-submitting the same id
     * (e.g. on double-click or browser retry) updates the existing row instead
     * of creating duplicates.
     */
    public function store(CookieConsentRequest $request): Response
    {
        $validated = $request->validated();

        $externalId = $request->cookie('coine_uid');
        $ipSalt = config('cookie-consent.ip_salt');

        CookieConsent::query()->updateOrCreate(
            ['consent_id' => $validated['consent_id']],
            [
                'external_id' => is_string($externalId) && $externalId !== '' ? $externalId : null,
                'necessary' => true,
                'marketing' => $validated['marketing'],
                'analytics' => $validated['analytics'],
                'choice_type' => $validated['choice_type'],
                'ip_hash' => $this->hashIp($request->ip(), $ipSalt),
                'user_agent' => $request->userAgent(),
                'referer' => $request->headers->get('referer'),
                'path' => $validated['path'],
                'version' => (string) config('cookie-consent.version'),
            ]
        );

        return response()->noContent();
    }

    private function hashIp(?string $ip, mixed $salt): ?string
    {
        if ($ip === null || $ip === '' || ! is_string($salt) || $salt === '') {
            return null;
        }

        return hash('sha256', $ip.$salt);
    }
}
