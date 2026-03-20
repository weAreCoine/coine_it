<?php

use App\Http\Controllers\ContactFormController;
use App\Http\Controllers\HealthCheckQuizController;
use App\Http\Controllers\Pages\AboutPageController;
use App\Http\Controllers\Pages\ArticlePageController;
use App\Http\Controllers\Pages\CategoryPageController;
use App\Http\Controllers\Pages\ContactPageController;
use App\Http\Controllers\Pages\ContentPageController;
use App\Http\Controllers\Pages\CookiePolicyPageController;
use App\Http\Controllers\Pages\DevelopingPageController;
use App\Http\Controllers\Pages\HealthCheckPageController;
use App\Http\Controllers\Pages\HealthCheckResultPageController;
use App\Http\Controllers\Pages\MarketingPageController;
use App\Http\Controllers\Pages\PrivacyPolicyPageController;
use App\Http\Controllers\Pages\ProjectPageController;
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

Route::get('/chi-siamo', [AboutPageController::class, 'show'])
    ->name('about');

Route::get('/health-check', [HealthCheckPageController::class, 'show'])
    ->name('health-check');
Route::post('/health-check', [HealthCheckQuizController::class, 'store'])
    ->name('health-check.store');
Route::patch('/health-check', [HealthCheckQuizController::class, 'complete'])
    ->name('health-check.complete');
Route::get('/health-check/{lead:uuid}', [HealthCheckResultPageController::class, 'show'])
    ->name('health-check.result');

Route::prefix('servizi')->group(function () {
    Route::get('sviluppo-app-siti-web', [DevelopingPageController::class, 'show'])
        ->name('service.developing');
    Route::get('marketing', [MarketingPageController::class, 'show'])
        ->name('service.marketing');
    Route::get('creazione-contenuti', [ContentPageController::class, 'show'])->name('service.content');
});

Route::prefix('contact')->group(function () {
    Route::get('/', [ContactPageController::class, 'show'])->name('contact.show');
    Route::post('/', [ContactFormController::class, 'store'])->name('contact.store');
});

Route::get('/cookie-policy', [CookiePolicyPageController::class, 'show'])
    ->name('cookie-policy');

Route::get('/privacy-policy', [PrivacyPolicyPageController::class, 'show'])
    ->name('privacy-policy');

Route::prefix('progetti')->group(function () {
    Route::get('/', [ProjectPageController::class, 'index'])->name('projects.index');
    Route::get('/{project:slug}', [ProjectPageController::class, 'show'])->name('projects.show');
});
