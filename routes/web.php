<?php

use App\Entities\NavigationItem;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'hero' => [
            'title' => 'Costruiamo insieme la tua presenza digitale',
            'description' => 'Aiutiamo gli e‑commerce a trasformare il marketing in un sistema misurabile lavorando su conversioni, vendite e marginalità.',
            'link' => new NavigationItem('Scopri di più', null, isCallToAction: true)
        ]
    ]);
})->name('home')->middleware('auth');
