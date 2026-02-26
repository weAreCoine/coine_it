<?php

use App\Entities\NavigationItem;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'hero' => [
            'title' => 'The future beyond human intelligence',
            'description' => 'Lorem ipsum dolor sit amet consectetur convallis ut et in id enim tempus quis amet consequat ut rhoncus morbi ullamcorper faucibus in natoque.',
            'link' => new NavigationItem('Scopri di più', null, isCallToAction: true)
        ]
    ]);
})->name('home')->middleware('auth');
