<?php

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectTag;
use App\Models\ProjectTool;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('returns 200 for a published project', function () {
    $project = Project::factory()
        ->for($this->user)
        ->create(['is_published' => true]);

    $this->get(route('projects.show', $project))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('projects/show'));
});

test('returns 404 for an unpublished project', function () {
    $project = Project::factory()
        ->for($this->user)
        ->create(['is_published' => false]);

    $this->get(route('projects.show', $project))
        ->assertNotFound();
});

test('returns 404 for a non-existent slug', function () {
    $this->get('/progetti/non-existent-slug-12345')
        ->assertNotFound();
});

test('returns correct project props including goal, tools, and results', function () {
    $category = ProjectCategory::forceCreate([
        'name' => 'Design',
        'slug' => 'design',
        'description' => 'Design projects',
    ]);

    $tag = ProjectTag::forceCreate([
        'name' => 'Laravel',
        'slug' => 'laravel',
        'description' => 'Laravel tag',
    ]);

    $tool = ProjectTool::forceCreate([
        'name' => 'Figma',
        'slug' => 'figma',
        'description' => 'Figma tool',
    ]);

    $project = Project::factory()
        ->for($this->user)
        ->create([
            'is_published' => true,
            'title' => 'Test Project',
            'slug' => 'test-project',
            'goal' => 'Build something great',
            'results' => 'Increased revenue by 200%',
            'cover' => null,
        ]);

    $project->categories()->attach($category);
    $project->tags()->attach($tag);
    $project->tools()->attach($tool);

    $this->get(route('projects.show', $project))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('title', 'Test Project')
            ->where('slug', 'test-project')
            ->where('goal', 'Build something great')
            ->where('results', 'Increased revenue by 200%')
            ->where('tools', ['Figma'])
            ->where('categories', ['Design'])
            ->where('tags', ['Laravel'])
            ->where('authorName', $this->user->name)
            ->where('cover', null)
            ->has('excerpt')
            ->has('content')
            ->has('createdAt')
            ->has('createdAtIso')
        );
});

test('seo falls back correctly when fields are null', function () {
    $project = Project::factory()
        ->for($this->user)
        ->create([
            'is_published' => true,
            'seo_title' => null,
            'seo_description' => null,
            'seo_image' => null,
            'cover' => null,
        ]);

    $this->get(route('projects.show', $project))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('seoTitle', $project->title)
            ->where('seoImage', null)
        );
});

test('seo uses custom values when provided', function () {
    $project = Project::factory()
        ->for($this->user)
        ->create([
            'is_published' => true,
            'seo_title' => 'Custom Project SEO',
            'seo_description' => 'Custom project description',
        ]);

    $this->get(route('projects.show', $project))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('seoTitle', 'Custom Project SEO')
            ->where('seoDescription', 'Custom project description')
        );
});

test('returns related projects from same categories, max 3', function () {
    $category = ProjectCategory::forceCreate([
        'name' => 'Shared',
        'slug' => 'shared',
        'description' => 'Shared category',
    ]);

    $project = Project::factory()
        ->for($this->user)
        ->create(['is_published' => true]);
    $project->categories()->attach($category);

    $related = Project::factory()
        ->count(4)
        ->for($this->user)
        ->create(['is_published' => true]);
    $related->each(fn ($p) => $p->categories()->attach($category));

    $this->get(route('projects.show', $project))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('relatedProjects', 3)
        );
});

test('related projects exclude unpublished and current project', function () {
    $category = ProjectCategory::forceCreate([
        'name' => 'Cat',
        'slug' => 'cat',
        'description' => 'Category',
    ]);

    $project = Project::factory()
        ->for($this->user)
        ->create(['is_published' => true]);
    $project->categories()->attach($category);

    $unpublished = Project::factory()
        ->for($this->user)
        ->create(['is_published' => false]);
    $unpublished->categories()->attach($category);

    $this->get(route('projects.show', $project))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('relatedProjects', 0)
        );
});
