<?php

namespace App\Http\Middleware;

use App\Entities\NavigationItem;
use App\Helpers\CookieConsent;
use App\Services\GoogleAnalytics\GoogleAnalyticsService;
use App\Services\LeadService;
use App\Services\LinkedIn\LinkedInService;
use Combindma\FacebookPixel\Facades\MetaPixel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $eventId = Str::uuid()->toString();
        $request->attributes->set('meta_pixel_event_id', $eventId);

        $isHealthCheck = $request->routeIs('health-check');

        $flashEvents = collect(session()->get(MetaPixel::sessionKey(), []))
            ->map(function (array $event, string $eventName): array {
                $data = $event['data'] ?? [];
                $isCustomEvent = ($data[LeadService::META_TRACK_METHOD_KEY] ?? null) === 'trackCustom';
                unset($data[LeadService::META_TRACK_METHOD_KEY]);

                return [
                    'eventName' => $eventName,
                    'data' => $data,
                    'eventId' => $event['event_id'] ?? null,
                    'isCustomEvent' => $isCustomEvent,
                ];
            })
            ->values()
            ->all();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'env' => config('app.env'),
            'auth' => [
                'user' => $request->user(),
            ],
            'navigationItems' => $isHealthCheck ? [] : [
                new NavigationItem('Home', route('home')),

                new NavigationItem('Chi siamo', route('about')),
                new NavigationItem('Servizi', '#', isPlaceholder: true, subItems: [
                    new NavigationItem('Sviluppo', route('service.developing')),
                    new NavigationItem('Marketing', route('service.marketing')),
                    new NavigationItem('Creazione Contenuti', route('service.content')),
                ]),
                new NavigationItem('Progetti', route('projects.index')),
                new NavigationItem('Blog', route('blog.index')),

                new NavigationItem('Testa il tuo sito', route('health-check'),
                    isCallToAction: true),
            ],
            'consent' => [
                'given' => CookieConsent::hasConsent(),
                'marketing' => CookieConsent::hasMarketingConsent(),
            ],
            'metaPixel' => [
                'eventId' => $eventId,
                'pixelId' => MetaPixel::pixelId(),
                'enabled' => MetaPixel::isEnabled(),
                'flashEvents' => $flashEvents,
            ],
            'googleAnalytics' => [
                'measurementId' => GoogleAnalyticsService::measurementId(),
                'enabled' => GoogleAnalyticsService::isEnabled(),
                'flashEvents' => session()->pull('ga4_flash_events', []),
            ],
            'linkedIn' => [
                'partnerId' => LinkedInService::partnerId(),
                'enabled' => LinkedInService::isEnabled(),
                'flashEvents' => session()->pull('linkedin_flash_events', []),
            ],
        ];
    }
}
