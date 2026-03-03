<?php

declare(strict_types=1);

namespace App\Entities;

use App\Models\Article;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Storage;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class BlogArticleCard implements Arrayable
{
    public string $title;

    public string $slug;

    public string $excerpt;

    public ?string $cover;

    /** @var BlogCategoryItem[] */
    public array $categories;

    public string $createdAt;

    public string $createdAtIso;

    public string $authorName;

    public static function fromArticle(Article $article): self
    {
        $instance = new self;
        $instance->title = $article->title;
        $instance->slug = $article->slug;
        $instance->excerpt = $article->excerpt;
        $instance->cover = $article->cover ? Storage::disk(Article::$disk)->url($article->cover) : null;
        $instance->categories = $article->categories
            ->map(fn ($category) => BlogCategoryItem::fromCategory($category))
            ->all();
        $instance->createdAt = $article->created_at->format('d M Y');
        $instance->createdAtIso = $article->created_at->toIso8601String();
        $instance->authorName = $article->user->name ?? '';

        return $instance;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return (array) $this;
    }
}
