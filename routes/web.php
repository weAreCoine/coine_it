<?php

use App\Http\Controllers\ContactFormController;
use App\Http\Controllers\Pages\AboutPageController;
use App\Http\Controllers\Pages\ArticlePageController;
use App\Http\Controllers\Pages\CategoryPageController;
use App\Http\Controllers\Pages\ContactPageController;
use App\Http\Controllers\Pages\WelcomePageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomePageController::class, 'show'])
    ->name('home');

Route::prefix('blog')->group(function () {
    Route::get('/', [ArticlePageController::class, 'index'])
        ->name('blog.index');

    Route::get('/{article:slug}', [ArticlePageController::class, 'show'])
        ->name('blog.show');

    Route::get('/category/{category:slug}', [CategoryPageController::class, 'show'])
        ->name('blog.category');
});

Route::get('/chi-shiamo', [AboutPageController::class, 'show'])
    ->name('about');

Route::prefix('contact')->group(function () {
    Route::get('/', [ContactPageController::class, 'show'])->name('contact.show');
    Route::post('/', [ContactFormController::class, 'store'])->name('contact.store');
});
