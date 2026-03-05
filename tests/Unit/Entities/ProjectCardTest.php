<?php

uses(Tests\TestCase::class, \Illuminate\Foundation\Testing\RefreshDatabase::class);

use App\Entities\ProjectCard;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('fromProject maps all fields correctly without cover', function () {
    $user = User::factory()->create(['name' => 'Project Author']);

    $category = ProjectCategory::forceCreate([
        'name' => 'Web',
        'slug' => 'web',
        'description' => 'Web projects',
    ]);

    $project = Project::factory()
        ->for($user)
        ->create([
            'title' => 'My Project',
            'slug' => 'my-project',
            'cover' => null,
            'is_published' => true,
        ]);
    $project->categories()->attach($category);
    $project->load(['categories', 'user']);

    $card = ProjectCard::fromProject($project);

    expect($card->title)->toBe('My Project')
        ->and($card->slug)->toBe('my-project')
        ->and($card->excerpt)->toBeString()
        ->and($card->cover)->toBeNull()
        ->and($card->categories)->toHaveCount(1)
        ->and($card->categories[0]->name)->toBe('Web')
        ->and($card->categories[0]->slug)->toBe('web')
        ->and($card->createdAt)->toBeString()
        ->and($card->createdAtIso)->toBeString()
        ->and($card->authorName)->toBe('Project Author');
});

test('fromProject generates cover URL when cover exists', function () {
    Storage::fake('public');
    Storage::disk('public')->put('covers/project.jpg', 'fake image');

    $user = User::factory()->create();
    $project = Project::factory()
        ->for($user)
        ->create([
            'cover' => 'covers/project.jpg',
            'is_published' => true,
        ]);
    $project->load(['categories', 'user']);

    $card = ProjectCard::fromProject($project);

    expect($card->cover)->toContain('covers/project.jpg');
});

test('toArray returns array representation', function () {
    $user = User::factory()->create();
    $project = Project::factory()
        ->for($user)
        ->create(['cover' => null, 'is_published' => true]);
    $project->load(['categories', 'user']);

    $card = ProjectCard::fromProject($project);
    $array = $card->toArray();

    expect($array)->toBeArray()
        ->and($array)->toHaveKeys(['title', 'slug', 'excerpt', 'cover', 'categories', 'createdAt', 'createdAtIso', 'authorName']);
});
