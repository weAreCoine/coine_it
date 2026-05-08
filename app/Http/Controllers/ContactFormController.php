<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Services\LeadService;
use Illuminate\Http\RedirectResponse;

class ContactFormController extends Controller
{
    public function store(ContactFormRequest $request, LeadService $leadService): RedirectResponse
    {
        $validated = $request->validated();

        $leadService->createAndTrack([
            'first_name' => $validated['firstName'],
            'last_name' => $validated['lastName'],
            'name' => sprintf('%s %s', $validated['firstName'], $validated['lastName']),
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'project' => $validated['message'],
            'terms' => $validated['termsAccepted'],
            'newsletter_opt_in' => $validated['newsletterOptIn'] ?? false,
        ], $request, $validated['metaEventId']);

        return back();
    }
}
