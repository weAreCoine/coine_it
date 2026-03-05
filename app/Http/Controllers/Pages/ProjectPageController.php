<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Abstracts\AbstractPageController;
use App\Entities\ProjectCard;
use App\Entities\ProjectCategoryItem;
use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProjectPageController extends AbstractPageController
{
    /**
     * Display the projects archive page with featured projects, category filters, and paginated listing.
     */
    public function index(Request $request): Response
    {
        $currentCategorySlug = $request->query('category');

        $currentCategory = $currentCategorySlug
            ? ProjectCategory::query()->where('slug', $currentCategorySlug)->first()
            : null;

        $featuredQuery = Project::query()
            ->where('is_published', true)
            ->where('is_featured', true)
            ->with(['categories', 'user'])
            ->latest();

        if ($currentCategory) {
            $featuredQuery->whereHas('categories', fn ($q) => $q->where('project_categories.id', $currentCategory->id));
        }

        $featured = $featuredQuery->limit(2)->get();

        if ($featured->count() < 2) {
            $fillCount = 2 - $featured->count();
            $excludeIds = $featured->pluck('id')->all();

            $fillQuery = Project::query()
                ->where('is_published', true)
                ->whereNotIn('id', $excludeIds)
                ->with(['categories', 'user'])
                ->latest();

            if ($currentCategory) {
                $fillQuery->whereHas('categories', fn ($q) => $q->where('project_categories.id', $currentCategory->id));
            }

            $fill = $fillQuery->limit($fillCount)->get();
            $featured = $featured->merge($fill);
        }

        $featuredIds = $featured->pluck('id')->all();

        $projectsQuery = Project::query()
            ->where('is_published', true)
            ->whereNotIn('id', $featuredIds)
            ->with(['categories', 'user'])
            ->latest();

        if ($currentCategory) {
            $projectsQuery->whereHas('categories', fn ($q) => $q->where('project_categories.id', $currentCategory->id));
        }

        $projects = $projectsQuery->paginate(6)->withQueryString();

        $categories = ProjectCategory::query()
            ->whereHas('projects', fn ($q) => $q->where('is_published', true))
            ->orderBy('name')
            ->get()
            ->map(fn (ProjectCategory $category) => ProjectCategoryItem::fromCategory($category))
            ->all();

        return Inertia::render('projects/index', [
            'featuredProjects' => $featured->map(fn (Project $project) => ProjectCard::fromProject($project))->all(),
            'projects' => $projects->through(fn (Project $project) => ProjectCard::fromProject($project)),
            'categories' => $categories,
            'currentCategory' => $currentCategorySlug,
            'seoTitle' => 'Progetti — Coine',
            'seoDescription' => 'I nostri progetti: case study, risultati e soluzioni digitali realizzate.',
            'canonicalUrl' => route('projects.index'),
        ]);
    }

    /**
     * Display a single project page with full details and related projects.
     */
    public function show(Project $project): Response
    {
        abort_unless($project->is_published, 404);

        $project->load(['categories', 'tags', 'tools', 'user']);

        $categoryIds = $project->categories->pluck('id');

        $relatedProjects = Project::query()
            ->where('is_published', true)
            ->where('id', '!=', $project->id)
            ->whereHas('categories', fn ($q) => $q->whereIn('project_categories.id', $categoryIds))
            ->with(['categories', 'user'])
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn (Project $related) => ProjectCard::fromProject($related))
            ->all();

        return Inertia::render('projects/show', [
            'title' => $project->title,
            'slug' => $project->slug,
            'content' => $project->content,
            'excerpt' => $project->excerpt,
            'cover' => $project->cover ? Storage::disk(Project::$disk)->url($project->cover) : null,
            'goal' => $project->goal,
            'tools' => $project->tools->pluck('name')->all(),
            'results' => $project->results,
            'categories' => $project->categories->pluck('name')->all(),
            'tags' => $project->tags->pluck('name')->all(),
            'authorName' => $project->user->name ?? '',
            'createdAt' => $project->created_at->format('d M Y'),
            'createdAtIso' => $project->created_at->toIso8601String(),
            'seoTitle' => $project->seo_title ?? $project->title,
            'seoDescription' => $project->seo_description ?? $project->excerpt,
            'seoImage' => $project->seo_image
                ? Storage::disk(Project::$disk)->url($project->seo_image)
                : ($project->cover ? Storage::disk(Project::$disk)->url($project->cover) : null),
            'canonicalUrl' => route('projects.show', $project),
            'relatedProjects' => $relatedProjects,
        ]);
    }
}
