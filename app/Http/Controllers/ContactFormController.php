<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Models\Lead;

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

    }
}
