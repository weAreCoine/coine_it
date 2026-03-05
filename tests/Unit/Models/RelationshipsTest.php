<?php

uses(Tests\TestCase::class, \Illuminate\Foundation\Testing\RefreshDatabase::class);

use App\Models\Article;
use App\Models\Category;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectTag;
use App\Models\ProjectTool;
use App\Models\Tag;
use App\Models\User;

test('Category has articles relationship', function () {
    $category = Category::factory()->create();
    $article = Article::factory()->create(['is_published' => true]);
    $category->articles()->attach($article);

    expect($category->articles)->toHaveCount(1);
});

test('Category has projects relationship', function () {
    $category = Category::factory()->create();
    $project = Project::factory()->create(['is_published' => true]);
    $category->projects()->attach($project);

    expect($category->projects)->toHaveCount(1);
});

test('Category route key name is slug', function () {
    $category = new Category;

    expect($category->getRouteKeyName())->toBe('slug');
});

test('Tag has articles relationship', function () {
    $tag = Tag::factory()->create();
    $article = Article::factory()->create(['is_published' => true]);
    $tag->articles()->attach($article);

    expect($tag->articles)->toHaveCount(1);
});

test('Tag has projects relationship', function () {
    $tag = Tag::factory()->create();
    $project = Project::factory()->create(['is_published' => true]);
    $tag->projects()->attach($project);

    expect($tag->projects)->toHaveCount(1);
});

test('ProjectCategory has projects relationship', function () {
    $category = ProjectCategory::forceCreate([
        'name' => 'ProjCat',
        'slug' => 'proj-cat',
        'description' => 'Test',
    ]);
    $project = Project::factory()->create(['is_published' => true]);
    $category->projects()->attach($project);

    expect($category->projects)->toHaveCount(1);
});

test('ProjectTag has projects relationship', function () {
    $tag = ProjectTag::forceCreate([
        'name' => 'ProjTag',
        'slug' => 'proj-tag',
        'description' => 'Test',
    ]);
    $project = Project::factory()->create(['is_published' => true]);
    $project->tags()->attach($tag);

    $tag->refresh();
    expect($tag->projects)->toHaveCount(1);
});

test('ProjectTool has projects relationship', function () {
    $tool = ProjectTool::forceCreate([
        'name' => 'ProjTool',
        'slug' => 'proj-tool',
        'description' => 'Test',
    ]);
    $project = Project::factory()->create(['is_published' => true]);
    $project->tools()->attach($tool);

    $tool->refresh();
    expect($tool->projects)->toHaveCount(1);
});

test('User has articles relationship', function () {
    $user = User::factory()->create();
    Article::factory()->for($user)->create();

    expect($user->articles)->toHaveCount(1);
});

test('User has projects relationship', function () {
    $user = User::factory()->create();
    Project::factory()->for($user)->create();

    expect($user->projects)->toHaveCount(1);
});

test('User canAccessPanel returns true', function () {
    $user = User::factory()->create();

    $panels = Filament\Facades\Filament::getPanels();
    $panel = reset($panels);

    expect($user->canAccessPanel($panel))->toBeTrue();
});
