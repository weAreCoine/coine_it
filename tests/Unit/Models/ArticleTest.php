<?php

uses(Tests\TestCase::class, \Illuminate\Foundation\Testing\RefreshDatabase::class);

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;

test('article has tags relationship', function () {
    $article = Article::factory()->create(['is_published' => true]);
    $tag = Tag::factory()->create();
    $article->tags()->attach($tag);

    expect($article->tags)->toHaveCount(1)
        ->and($article->tags->first()->id)->toBe($tag->id);
});

test('article has categories relationship', function () {
    $article = Article::factory()->create(['is_published' => true]);
    $category = Category::factory()->create();
    $article->categories()->attach($category);

    expect($article->categories)->toHaveCount(1)
        ->and($article->categories->first()->id)->toBe($category->id);
});

test('article has user relationship', function () {
    $user = User::factory()->create();
    $article = Article::factory()->for($user)->create();

    expect($article->user->id)->toBe($user->id);
});

test('route key name is slug', function () {
    $article = new Article;

    expect($article->getRouteKeyName())->toBe('slug');
});

test('casts is_published and is_featured as boolean', function () {
    $article = Article::factory()->create([
        'is_published' => 1,
        'is_featured' => 0,
    ]);

    expect($article->is_published)->toBeTrue()
        ->and($article->is_featured)->toBeFalse();
});

test('article uses SoftDeletes', function () {
    $article = Article::factory()->create();
    $articleId = $article->id;

    $article->delete();

    expect(Article::find($articleId))->toBeNull()
        ->and(Article::withTrashed()->find($articleId))->not->toBeNull();
});

test('excerpt property hook returns truncated content', function () {
    $article = Article::factory()->create([
        'content' => str_repeat('word ', 200),
    ]);

    expect($article->excerpt)->toBeString()
        ->and(strlen($article->excerpt))->toBeLessThan(strlen($article->content));
});
