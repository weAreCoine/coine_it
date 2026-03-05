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
                new Faq('A chi è rivolta la vostra consulenza?',
                    'Per e-commerce e aziende digitali che vogliono trasformare il marketing in un sistema misurabile e prendere decisioni basate sui dati.'),
                new Faq('Lavorate anche con progetti in partenza?',
                    'Sì, il nostro approccio si adatta sia a e-commerce già attivi sia a nuovi progetti digitali.'),
                new Faq('Quanto dura la consulenza iniziale?',
                    'Circa 30-45 minuti: basta per capire contesto, criticità e priorità del tuo business.'),
                new Faq('Che risultati posso aspettarmi?',
                    'Non promettiamo numeri immediati. Ti forniamo chiarezza, strumenti misurabili e un metodo per decisioni consapevoli e crescita sostenibile.'),
                new Faq('Devo usare piattaforme specifiche?',
                    'No. Lavoriamo con WooCommerce, Shopify, WordPress e i principali strumenti di marketing, adattando la strategia al tuo ecosistema.'),
                new Faq('Posso proseguire dopo la consulenza iniziale?',
                    'Assolutamente. Puoi continuare con affiancamento strategico e ottimizzazione continua di campagne, funnel e dati.'),
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
