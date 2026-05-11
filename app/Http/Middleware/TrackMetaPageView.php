<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Helpers\CookieConsent;
use App\Jobs\SendMetaConversionEventJob;
use App\Services\Meta\MetaPixelUserDataFactory;
use Closure;
use Combindma\FacebookPixel\Facades\MetaPixel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TrackMetaPageView
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! MetaPixel::isEnabled() || ! CookieConsent::hasMarketingConsent()) {
            return $response;
        }

        if ($request->is('api/*')) {
            return $response;
        }

        $eventId = $request->attributes->get('meta_pixel_event_id');

        if (! $eventId) {
            return $response;
        }

        try {
            $userData = MetaPixelUserDataFactory::make(...self::userDataParamsFor($request));

            SendMetaConversionEventJob::send(
                'PageView',
                $eventId,
                $userData,
                eventSourceUrl: $request->fullUrl(),
            );
        } catch (\Exception $e) {
            Log::error('Meta Pixel CAPI PageView failed', ['error' => $e->getMessage()]);
        }

        return $response;
    }

    /**
     * Build the optional contact parameters fed to the factory from the
     * authenticated user when available. We only have name + email today, so
     * the firstName/lastName split is best-effort: the first token is taken
     * as the first name and the remainder as the last name.
     *
     * @return array<string, ?string>
     */
    private static function userDataParamsFor(Request $request): array
    {
        $user = $request->user();

        if ($user === null) {
            return [];
        }

        $params = ['email' => $user->email];

        $name = trim((string) ($user->name ?? ''));

        if ($name !== '') {
            $parts = preg_split('/\s+/u', $name, 2) ?: [];
            $params['firstName'] = $parts[0] ?? null;
            $params['lastName'] = $parts[1] ?? null;
        }

        return $params;
    }
}
