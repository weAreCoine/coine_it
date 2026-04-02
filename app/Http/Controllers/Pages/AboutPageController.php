<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class AboutPageController extends Controller
{
    public function show()
    {
        $seoTitle = __('Chi siamo — Coiné');
        $seoDescription = __('Professionisti senior su advertising, sviluppo e contenuti. Zero fornitori da coordinare, zero tempo perso. Oltre 10 anni di esperienza dal fashion al biomedicale.');
        $canonicalUrl = route('about');

        return Inertia::render('about', [
            'numbers' => [
                [
                    'scalar' => '10+',
                    'description' => __('Anni di esperienza'),
                ],
                [
                    'scalar' => '30+',
                    'description' => __('Brand gestiti'),
                ],
                [
                    'scalar' => '500+',
                    'description' => __('Campagne gestite'),
                ],
                [
                    'scalar' => '14+',
                    'description' => __('Collaborazioni'),
                ],
            ],
            'principles' => [
                [
                    'image' => 'https://cdn.prod.website-files.com/68a342b7066c56fa60eb3af1/68a598eeb838c974f3def70a_accuracy-icon-quantum-webflow-template-1.svg',
                    'title' => __('Accuratezza'),
                ],
                [
                    'image' => 'https://cdn.prod.website-files.com/68a342b7066c56fa60eb3af1/68a598f0e3caa08df665212f_trust-icon-quantum-webflow-template.svg',
                    'title' => __('Fiducia'),
                ],
                [
                    'image' => 'https://cdn.prod.website-files.com/68a342b7066c56fa60eb3af1/68a598f1f8443cbbcf767239_intellect-icon-quantum-webflow-template.svg',
                    'title' => __('Innovazione'),
                ],
                [
                    'image' => 'https://cdn.prod.website-files.com/68a342b7066c56fa60eb3af1/68a598f00141b9ac31a01989_velocity-icon-quantum-webflow-template.svg',
                    'title' => __('Velocità'),
                ],
                [
                    'image' => 'https://cdn.prod.website-files.com/68a342b7066c56fa60eb3af1/68a598eefda8dc1927236eb3_clarity-icon-quantum-webflow-template.svg',
                    'title' => __('Chiarezza'),
                ],
                [
                    'image' => 'https://cdn.prod.website-files.com/68a342b7066c56fa60eb3af1/68a598f04600b3080cab823c_scalability-icon-quantum-webflow-template.svg',
                    'title' => __('Scalabilità'),
                ],
                [
                    'image' => 'https://cdn.prod.website-files.com/68a342b7066c56fa60eb3af1/68a598ed8d91f474a2d4ecdc_excellence-icon-quantum-webflow-template.svg',
                    'title' => __('Eccellenza'),
                ],
                [
                    'image' => 'https://cdn.prod.website-files.com/68a342b7066c56fa60eb3af1/68a598ed19f8b3ed79933a38_communication-icon-quantum-webflow-template.svg',
                    'title' => __('Comunicazione'),
                ],
            ],
        ])->withViewData([
            'seoTitle' => $seoTitle,
            'seoDescription' => $seoDescription,
            'canonicalUrl' => $canonicalUrl,
        ]);
    }
}
