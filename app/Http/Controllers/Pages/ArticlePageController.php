<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Entities\BlogArticleCard;
use App\Http\Controllers\Controller;
use App\Models\Article;
use Inertia\Inertia;
use Inertia\Response;

class ArticlePageController extends Controller
{
    public function show(Article $article): Response
    {
        abort_unless($article->is_published, 404);

        $article->load(['categories', 'tags', 'user']);

        $categoryIds = $article->categories->pluck('id');

        $relatedArticles = Article::query()
            ->where('is_published', true)
            ->where('id', '!=', $article->id)
            ->whereHas('categories', fn($q) => $q->whereIn('categories.id', $categoryIds))
            ->with(['categories', 'user'])
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn(Article $related) => BlogArticleCard::fromArticle($related))
            ->all();

        return Inertia::render('blog/show', [
            'title' => $article->title,
            'slug' => $article->slug,
            'content' => $article->content,
            'excerpt' => $article->excerpt,
            'cover' => asset($article->cover),
            'categories' => $article->categories->pluck('name')->all(),
            'tags' => $article->tags->pluck('name')->all(),
            'authorName' => $article->user->name ?? '',
            'createdAt' => $article->created_at->format('d M Y'),
            'createdAtIso' => $article->created_at->toIso8601String(),
            'seoTitle' => $article->seo_title ?? $article->title,
            'seoDescription' => $article->seo_description ?? $article->excerpt,
            'seoImage' => $article->seo_image ?? $article->cover,
            'canonicalUrl' => route('blog.show', $article),
            'relatedArticles' => $relatedArticles,
        ]);
    }
}
