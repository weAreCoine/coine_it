<?php

use App\Http\Controllers\Pages\ArticlePageController;
use App\Http\Controllers\Pages\CategoryPageController;
use App\Http\Controllers\Pages\WelcomePageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomePageController::class, 'show'])
    ->name('home');

Route::get('/blog/{article:slug}', [ArticlePageController::class, 'show'])
    ->name('blog.show');

Route::get('/blog/category/{category:slug}', [CategoryPageController::class, 'show'])
    ->name('blog.category');
