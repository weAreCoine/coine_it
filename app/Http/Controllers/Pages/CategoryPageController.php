<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Entities\BlogArticleCard;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Inertia\Inertia;
use Inertia\Response;

class CategoryPageController extends Controller
{
    public function show(Category $category): Response
    {
        $articles = Article::query()
            ->where('is_published', true)
            ->whereHas('categories', fn ($q) => $q->where('categories.id', $category->id))
            ->with(['categories', 'user'])
            ->latest()
            ->paginate(9);

        $seoTitle = $category->name.' — Blog Coiné';
        $seoDescription = __('Articoli e approfondimenti nella categoria :category.', ['category' => $category->name]);
        $canonicalUrl = route('blog.category', $category);

        return Inertia::render('blog/category', [
            'name' => $category->name,
            'slug' => $category->slug,
            'articles' => $articles->through(fn (Article $article) => BlogArticleCard::fromArticle($article)),
        ])->withViewData([
            'seoTitle' => $seoTitle,
            'seoDescription' => $seoDescription,
            'canonicalUrl' => $canonicalUrl,
        ]);
    }
}
