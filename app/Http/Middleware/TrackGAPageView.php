<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\GoogleAnalytics\GoogleAnalyticsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackGAPageView
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->is('api/*')) {
            return $response;
        }

        GoogleAnalyticsService::trackPageView($request);

        return $response;
    }
}
