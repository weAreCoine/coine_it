<?php

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

use App\Models\Category;
use App\Models\ProjectCategory;
use App\Models\ProjectTag;
use App\Models\ProjectTool;
use App\Models\Tag;

test('slug is generated from name on creation', function () {
    $tag = Tag::factory()->create(['name' => 'My Tag Name']);

    expect($tag->slug)->toBe('my-tag-name');
});

test('slug is regenerated when name changes', function () {
    $tag = Tag::factory()->create(['name' => 'Original Name']);

    $tag->update(['name' => 'Updated Name']);

    expect($tag->fresh()->slug)->toBe('updated-name');
});

test('slug is not overwritten if manually set on creation', function () {
    $tag = Tag::factory()->create([
        'name' => 'My Tag',
        'slug' => 'custom-slug',
    ]);

    expect($tag->slug)->toBe('custom-slug');
});

test('all taxonomy models generate slug on creation', function (string $modelClass, array $attributes) {
    $model = $modelClass::factory()->create($attributes);

    expect($model->slug)->toBe('test-name');
})->with([
    'Tag' => [Tag::class, ['name' => 'Test Name']],
    'Category' => [Category::class, ['name' => 'Test Name']],
    'ProjectTag' => [ProjectTag::class, ['name' => 'Test Name']],
    'ProjectCategory' => [ProjectCategory::class, ['name' => 'Test Name']],
    'ProjectTool' => [ProjectTool::class, ['name' => 'Test Name']],
]);
