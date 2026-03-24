<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ExceptionHandler;
use App\Helpers\CookieConsent;
use App\Models\Lead;
use App\Services\GoogleAnalytics\GoogleAnalyticsService;
use App\Services\LinkedIn\LinkedInService;
use Combindma\FacebookPixel\Facades\MetaPixel;
use FacebookAds\Object\ServerSide\CustomData;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LeadService
{
    /**
     * Create a new lead and fire tracking events.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function createAndTrack(array $attributes, Request $request, string $gaEventName = 'generate_lead'): Lead
    {
        $lead = Lead::create($attributes);

        $this->trackMetaPixelEvent('Lead');
        $this->trackGAEvent($request, $gaEventName);
        $this->trackLinkedInEvent($request, 'lead', $attributes['email'] ?? null);

        return $lead;
    }

    /**
     * Send a server-side Meta Pixel event and flash it for the browser pixel.
     */
    public function trackMetaPixelEvent(string $eventName): void
    {
        if (! MetaPixel::isEnabled() || ! CookieConsent::hasMarketingConsent()) {
            return;
        }

        $eventId = Str::uuid()->toString();

        try {
            MetaPixel::send($eventName, $eventId, new CustomData);
        } catch (\Exception $e) {
            ExceptionHandler::handle($e);
        }

        MetaPixel::flashEvent($eventName, [], $eventId);
    }

    /**
     * Track a Google Analytics event and flash it for the next page view.
     */
    public function trackGAEvent(Request $request, string $eventName): void
    {
        GoogleAnalyticsService::trackGenerateLead($request);
        GoogleAnalyticsService::flashEvent($eventName);
    }

    /**
     * Send a server-side LinkedIn conversion event and flash it for the browser.
     */
    public function trackLinkedInEvent(Request $request, string $conversionType, ?string $email = null): void
    {
        LinkedInService::trackConversion($request, $conversionType, $email);
        LinkedInService::flashEvent($conversionType);
    }
}
