<?php

namespace App\Http\Middleware;

use App\Entities\NavigationItem;
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

        $flashEvents = collect(session()->get(MetaPixel::sessionKey(), []))
            ->map(fn (array $event, string $eventName) => [
                'eventName' => $eventName,
                'data' => $event['data'] ?? [],
                'eventId' => $event['event_id'] ?? null,
            ])
            ->values()
            ->all();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'env' => config('app.env'),
            'auth' => [
                'user' => $request->user(),
            ],
            'navigationItems' => [
                new NavigationItem('Home', route('home')),

                new NavigationItem('Chi siamo', route('about')),
                new NavigationItem('Servizi', '#', isPlaceholder: true, subItems: [
                    new NavigationItem('Sviluppo', route('service.developing')),
                    new NavigationItem('Marketing', route('service.marketing')),
                    new NavigationItem('Creazione Contenuti', route('service.content')),
                ]),
                new NavigationItem('Progetti', route('projects.index')),
                new NavigationItem('Blog', route('blog.index')),

                new NavigationItem('Scrivici', route('contact.show'), isCallToAction: true),
            ],
            'metaPixel' => [
                'eventId' => $eventId,
                'pixelId' => MetaPixel::pixelId(),
                'enabled' => MetaPixel::isEnabled(),
                'flashEvents' => $flashEvents,
            ],
        ];
    }
}
