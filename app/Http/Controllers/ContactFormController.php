<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Services\LeadService;

class ContactFormController extends Controller
{
    public function store(ContactFormRequest $request, LeadService $leadService): void
    {
        $validated = $request->validated();

        $leadService->createAndTrack([
            'name' => sprintf('%s %s', $validated['firstName'], $validated['lastName']),
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'project' => $validated['message'],
            'terms' => $validated['termsAccepted'],
        ], $request);
    }
}
