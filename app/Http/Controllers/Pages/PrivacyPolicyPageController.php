<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class PrivacyPolicyPageController extends Controller
{
    public function show()
    {
        return Inertia::render('legal/privacy-policy')
            ->withViewData([
                'seoTitle' => __('Privacy Policy — Coiné'),
                'seoDescription' => __('Informativa sulla privacy e sul trattamento dei dati personali di Coiné.'),
                'canonicalUrl' => route('privacy-policy'),
                'seoRobots' => 'noindex, nofollow',
            ]);
    }
}
