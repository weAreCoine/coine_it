<?php
declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class AboutPageController extends Controller
{
    public function show()
    {
        return Inertia::render('about', [
            'numbers' => [
                [
                    'scalar' => '10+',
                    'description' => __('Anni di esperienza, dal fashion al biomedicale'),
                ],
                [
                    'scalar' => '30+',
                    'description' => __('Brand gestiti, da PMI a Multinazionali'),
                ],
                [
                    'scalar' => '500+',
                    'description' => __('Campagne advertising gestite e ottimizzate'),
                ],
                [
                    'scalar' => '14+',
                    'description' => __('Collaborazioni'),
                ],
            ]

        ]);
    }
}
