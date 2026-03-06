<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\IubendaHelper;
use App\Http\Requests\ContactFormRequest;
use App\Models\Lead;
use Combindma\FacebookPixel\Facades\MetaPixel;
use FacebookAds\Object\ServerSide\CustomData;
use Illuminate\Support\Str;

class ContactFormController extends Controller
{
    public function store(ContactFormRequest $request)
    {
        $validated = $request->validated();
        Lead::create([
            'name' => sprintf('%s %s', $validated['firstName'], $validated['lastName']),
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'project' => $validated['message'],
            'terms' => $validated['termsAccepted'],
        ]);

        $this->trackLeadEvent();
    }

    private function trackLeadEvent(): void
    {
        if (! MetaPixel::isEnabled() || ! IubendaHelper::hasExperienceConsent()) {
            return;
        }

        $leadEventId = Str::uuid()->toString();

        try {
            MetaPixel::send('Lead', $leadEventId, new CustomData);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Meta Pixel CAPI Lead failed', ['error' => $e->getMessage()]);
        }

        MetaPixel::flashEvent('Lead', [], $leadEventId);
    }
}
