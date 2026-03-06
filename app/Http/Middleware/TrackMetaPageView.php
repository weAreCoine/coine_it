<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Helpers\IubendaHelper;
use Closure;
use Combindma\FacebookPixel\Facades\MetaPixel;
use FacebookAds\Object\ServerSide\CustomData;
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

        if (! MetaPixel::isEnabled()) {
            return $response;
        }

        if (! IubendaHelper::hasExperienceConsent()) {
            return $response;
        }

        if ($request->is('api/*') || IubendaHelper::bot_detected()) {
            return $response;
        }

        $eventId = $request->attributes->get('meta_pixel_event_id');

        if (! $eventId) {
            return $response;
        }

        try {
            MetaPixel::send('PageView', $eventId, new CustomData);
        } catch (\Exception $e) {
            Log::error('Meta Pixel CAPI PageView failed', ['error' => $e->getMessage()]);
        }

        return $response;
    }
}
