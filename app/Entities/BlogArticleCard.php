<?php

declare(strict_types=1);

namespace App\Entities;

use App\Models\Article;
use Illuminate\Contracts\Support\Arrayable;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class BlogArticleCard implements Arrayable
{
    public string $title;

    public string $slug;

    public string $excerpt;

    public ?string $cover;

    /** @var array<int, array{name: string, slug: string}> */
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
        $instance->cover = $article->cover;
        $instance->categories = $article->categories->pluck('name')->implode(', ');
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
