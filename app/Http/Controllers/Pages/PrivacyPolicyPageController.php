<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class PrivacyPolicyPageController extends Controller
{
    public function show()
    {
        return Inertia::render('legal/privacy-policy');
    }
}
