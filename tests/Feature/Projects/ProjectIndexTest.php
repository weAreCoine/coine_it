<?php

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('returns 200 and renders projects/index component', function () {
    $this->get(route('projects.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('projects/index'));
});

test('returns max 2 featured published projects', function () {
    Project::factory()
        ->count(3)
        ->for($this->user)
        ->create(['is_published' => true, 'is_featured' => true]);

    $this->get(route('projects.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('featuredProjects', 2)
        );
});

test('fills featured from non-featured when less than 2 featured exist', function () {
    Project::factory()
        ->for($this->user)
        ->create(['is_published' => true, 'is_featured' => true]);

    Project::factory()
        ->for($this->user)
        ->create(['is_published' => true, 'is_featured' => false]);

    $this->get(route('projects.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('featuredProjects', 2)
        );
});

test('paginates projects at 6 per page excluding featured', function () {
    Project::factory()
        ->count(10)
        ->for($this->user)
        ->create(['is_published' => true, 'is_featured' => false]);

    $this->get(route('projects.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('projects.data', 6)
        );
});

test('excludes unpublished projects', function () {
    Project::factory()
        ->for($this->user)
        ->create(['is_published' => false]);

    $this->get(route('projects.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('featuredProjects', 0)
            ->has('projects.data', 0)
        );
});

test('filters by category query parameter', function () {
    $category = ProjectCategory::forceCreate([
        'name' => 'Web Dev',
        'slug' => 'web-dev',
        'description' => 'Web development projects',
    ]);

    $project = Project::factory()
        ->for($this->user)
        ->create(['is_published' => true, 'is_featured' => false]);
    $project->categories()->attach($category);

    $otherCategory = ProjectCategory::forceCreate([
        'name' => 'Marketing',
        'slug' => 'marketing',
        'description' => 'Marketing projects',
    ]);

    $excluded = Project::factory()
        ->for($this->user)
        ->create(['is_published' => true, 'is_featured' => false]);
    $excluded->categories()->attach($otherCategory);

    $this->get(route('projects.index', ['category' => 'web-dev']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('currentCategory', 'web-dev')
        );
});

test('returns categories that have at least one published project', function () {
    $usedCategory = ProjectCategory::forceCreate([
        'name' => 'Active',
        'slug' => 'active',
        'description' => 'Active category',
    ]);

    $project = Project::factory()
        ->for($this->user)
        ->create(['is_published' => true]);
    $project->categories()->attach($usedCategory);

    ProjectCategory::forceCreate([
        'name' => 'Empty',
        'slug' => 'empty',
        'description' => 'Empty category',
    ]);

    $this->get(route('projects.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('categories', 1)
            ->where('categories.0.slug', 'active')
        );
});

test('returns correct SEO props', function () {
    $this->get(route('projects.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('seoTitle', 'Progetti — Coine')
            ->has('seoDescription')
            ->has('canonicalUrl')
        );
});
