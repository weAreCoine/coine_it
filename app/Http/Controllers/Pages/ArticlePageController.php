<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Entities\BlogArticleCard;
use App\Entities\BlogCategoryItem;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ArticlePageController extends Controller
{
    /**
     * Display the blog archive page with featured articles, category filters, and paginated listing.
     */
    public function index(Request $request): Response
    {
        $currentCategorySlug = $request->query('category');

        $currentCategory = $currentCategorySlug
            ? Category::query()->where('slug', $currentCategorySlug)->first()
            : null;

        $featuredQuery = Article::query()
            ->where('is_published', true)
            ->where('is_featured', true)
            ->with(['categories', 'user'])
            ->latest();

        if ($currentCategory) {
            $featuredQuery->whereHas('categories', fn ($q) => $q->where('categories.id', $currentCategory->id));
        }

        $featured = $featuredQuery->limit(2)->get();

        if ($featured->count() < 2) {
            $fillCount = 2 - $featured->count();
            $excludeIds = $featured->pluck('id')->all();

            $fillQuery = Article::query()
                ->where('is_published', true)
                ->whereNotIn('id', $excludeIds)
                ->with(['categories', 'user'])
                ->latest();

            if ($currentCategory) {
                $fillQuery->whereHas('categories', fn ($q) => $q->where('categories.id', $currentCategory->id));
            }

            $fill = $fillQuery->limit($fillCount)->get();
            $featured = $featured->merge($fill);
        }

        $featuredIds = $featured->pluck('id')->all();

        $articlesQuery = Article::query()
            ->where('is_published', true)
            ->whereNotIn('id', $featuredIds)
            ->with(['categories', 'user'])
            ->latest();

        if ($currentCategory) {
            $articlesQuery->whereHas('categories', fn ($q) => $q->where('categories.id', $currentCategory->id));
        }

        $articles = $articlesQuery->paginate(6)->withQueryString();

        $categories = Category::query()
            ->whereHas('articles', fn ($q) => $q->where('is_published', true))
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category) => BlogCategoryItem::fromCategory($category))
            ->all();

        return Inertia::render('blog/index', [
            'featuredArticles' => $featured->map(fn (Article $article) => BlogArticleCard::fromArticle($article))->all(),
            'articles' => $articles->through(fn (Article $article) => BlogArticleCard::fromArticle($article)),
            'categories' => $categories,
            'currentCategory' => $currentCategorySlug,
            'seoTitle' => 'Blog — Coine',
            'seoDescription' => 'Articoli, guide e approfondimenti su sviluppo web, design e tecnologia.',
            'canonicalUrl' => route('blog.index'),
        ]);
    }

    public function show(Article $article): Response
    {
        abort_unless($article->is_published, 404);

        $article->load(['categories', 'tags', 'user']);

        $categoryIds = $article->categories->pluck('id');

        $relatedArticles = Article::query()
            ->where('is_published', true)
            ->where('id', '!=', $article->id)
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds))
            ->with(['categories', 'user'])
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn (Article $related) => BlogArticleCard::fromArticle($related))
            ->all();

        return Inertia::render('blog/show', [
            'title' => $article->title,
            'slug' => $article->slug,
            'content' => $article->content,
            'excerpt' => $article->excerpt,
            'cover' => $article->cover ? Storage::disk(Article::$disk)->url($article->cover) : null,
            'categories' => $article->categories->map(fn (Category $cat) => [
                'name' => $cat->name,
                'slug' => $cat->slug,
            ])->all(),
            'tags' => $article->tags->map(fn (\App\Models\Tag $tag) => [
                'name' => $tag->name,
                'slug' => $tag->slug,
            ])->all(),
            'authorName' => $article->user->name ?? '',
            'createdAt' => $article->created_at->format('d M Y'),
            'createdAtIso' => $article->created_at->toIso8601String(),
            'seoTitle' => $article->seo_title ?? $article->title,
            'seoDescription' => $article->seo_description ?? $article->excerpt,
            'seoImage' => $article->seo_image
                ? Storage::disk(Article::$disk)->url($article->seo_image)
                : ($article->cover ? Storage::disk(Article::$disk)->url($article->cover) : null),
            'canonicalUrl' => route('blog.show', $article),
            'relatedArticles' => $relatedArticles,
        ]);
    }
}
