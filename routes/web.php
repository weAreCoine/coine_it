<?php

use App\Http\Controllers\Pages\WelcomePageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomePageController::class, 'show'])
    ->name('home');
