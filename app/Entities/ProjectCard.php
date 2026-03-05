<?php

declare(strict_types=1);

namespace App\Entities;

use App\Models\Project;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Storage;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ProjectCard implements Arrayable
{
    public string $title;

    public string $slug;

    public string $excerpt;

    public ?string $cover;

    /** @var ProjectCategoryItem[] */
    public array $categories;

    public string $createdAt;

    public string $createdAtIso;

    public string $authorName;

    public static function fromProject(Project $project): self
    {
        $instance = new self;
        $instance->title = $project->title;
        $instance->slug = $project->slug;
        $instance->excerpt = $project->excerpt;
        $instance->cover = $project->cover ? Storage::disk(Project::$disk)->url($project->cover) : null;
        $instance->categories = $project->categories
            ->map(fn ($category) => ProjectCategoryItem::fromCategory($category))
            ->all();
        $instance->createdAt = $project->created_at->format('d M Y');
        $instance->createdAtIso = $project->created_at->toIso8601String();
        $instance->authorName = $project->user->name ?? '';

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
