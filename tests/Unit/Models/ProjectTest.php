<?php

uses(Tests\TestCase::class, \Illuminate\Foundation\Testing\RefreshDatabase::class);

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectTag;
use App\Models\ProjectTool;
use App\Models\User;

test('project has tags relationship', function () {
    $project = Project::factory()->create(['is_published' => true]);
    $tag = ProjectTag::forceCreate(['name' => 'React', 'slug' => 'react', 'description' => 'React tag']);
    $project->tags()->attach($tag);

    expect($project->tags)->toHaveCount(1)
        ->and($project->tags->first()->id)->toBe($tag->id);
});

test('project has categories relationship', function () {
    $project = Project::factory()->create(['is_published' => true]);
    $category = ProjectCategory::forceCreate(['name' => 'Web', 'slug' => 'web', 'description' => 'Web category']);
    $project->categories()->attach($category);

    expect($project->categories)->toHaveCount(1)
        ->and($project->categories->first()->id)->toBe($category->id);
});

test('project has tools relationship', function () {
    $project = Project::factory()->create(['is_published' => true]);
    $tool = ProjectTool::forceCreate(['name' => 'Laravel', 'slug' => 'laravel', 'description' => 'Laravel tool']);
    $project->tools()->attach($tool);

    expect($project->tools)->toHaveCount(1)
        ->and($project->tools->first()->id)->toBe($tool->id);
});

test('project has user relationship', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();

    expect($project->user->id)->toBe($user->id);
});

test('route key name is slug', function () {
    $project = new Project;

    expect($project->getRouteKeyName())->toBe('slug');
});

test('casts is_published and is_featured as boolean', function () {
    $project = Project::factory()->create([
        'is_published' => 1,
        'is_featured' => 0,
    ]);

    expect($project->is_published)->toBeTrue()
        ->and($project->is_featured)->toBeFalse();
});

test('project uses SoftDeletes', function () {
    $project = Project::factory()->create();
    $projectId = $project->id;

    $project->delete();

    expect(Project::find($projectId))->toBeNull()
        ->and(Project::withTrashed()->find($projectId))->not->toBeNull();
});

test('excerpt property hook returns truncated content', function () {
    $project = Project::factory()->create([
        'content' => str_repeat('word ', 200),
    ]);

    expect($project->excerpt)->toBeString()
        ->and(strlen($project->excerpt))->toBeLessThan(strlen($project->content));
});
