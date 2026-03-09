<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class CookiePolicyPageController extends Controller
{
    public function show()
    {
        return Inertia::render('legal/cookie-policy');
    }
}
