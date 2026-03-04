<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Entities\Faq;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactPageController extends Controller
{
    public function show()
    {
        return Inertia::render('contact', [
            'faqs' => [
                new Faq('Domanda di esempio', 'Risposta di esempio'),
                new Faq('Domanda di esempio', 'Risposta di esempio'),
                new Faq('Domanda di esempio', 'Risposta di esempio'),
                new Faq('Domanda di esempio', 'Risposta di esempio'),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email',
            'phone' => 'sometimes|nullable',
            'message' => 'required',
            'termsAccepted' => 'accepted',
        ]);
    }
}
