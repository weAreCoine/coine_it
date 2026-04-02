<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class CookiePolicyPageController extends Controller
{
    public function show()
    {
        return Inertia::render('legal/cookie-policy')
            ->withViewData([
                'seoTitle' => __('Cookie Policy — Coiné'),
                'seoDescription' => __('Informativa sull\'utilizzo dei cookie sul sito Coiné.'),
                'canonicalUrl' => route('cookie-policy'),
                'seoRobots' => 'noindex, nofollow',
            ]);
    }
}
