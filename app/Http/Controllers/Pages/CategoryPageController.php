<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Inertia\Inertia;
use Inertia\Response;

class CategoryPageController extends Controller
{
    public function show(Category $category): Response
    {
        $seoTitle = $category->name.' — Blog Coiné';
        $seoDescription = __('Articoli e approfondimenti nella categoria :category.', ['category' => $category->name]);
        $canonicalUrl = route('blog.category', $category);

        return Inertia::render('blog/category', [
            'name' => $category->name,
            'slug' => $category->slug,
        ])->withViewData([
            'seoTitle' => $seoTitle,
            'seoDescription' => $seoDescription,
            'canonicalUrl' => $canonicalUrl,
        ]);
    }
}
