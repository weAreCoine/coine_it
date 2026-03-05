<?php

uses(Tests\TestCase::class, \Illuminate\Foundation\Testing\RefreshDatabase::class);

use App\Entities\BlogArticleCard;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('fromArticle maps all fields correctly without cover', function () {
    $user = User::factory()->create(['name' => 'Author Name']);
    $category = Category::factory()->create(['name' => 'Tech', 'slug' => 'tech']);

    $article = Article::factory()
        ->for($user)
        ->create([
            'title' => 'My Article',
            'slug' => 'my-article',
            'cover' => null,
            'is_published' => true,
        ]);
    $article->categories()->attach($category);
    $article->load(['categories', 'user']);

    $card = BlogArticleCard::fromArticle($article);

    expect($card->title)->toBe('My Article')
        ->and($card->slug)->toBe('my-article')
        ->and($card->excerpt)->toBeString()
        ->and($card->cover)->toBeNull()
        ->and($card->categories)->toHaveCount(1)
        ->and($card->categories[0]->name)->toBe('Tech')
        ->and($card->categories[0]->slug)->toBe('tech')
        ->and($card->createdAt)->toBeString()
        ->and($card->createdAtIso)->toBeString()
        ->and($card->authorName)->toBe('Author Name');
});

test('fromArticle generates cover URL when cover exists', function () {
    Storage::fake('public');
    Storage::disk('public')->put('covers/test.jpg', 'fake image');

    $user = User::factory()->create();
    $article = Article::factory()
        ->for($user)
        ->create([
            'cover' => 'covers/test.jpg',
            'is_published' => true,
        ]);
    $article->load(['categories', 'user']);

    $card = BlogArticleCard::fromArticle($article);

    expect($card->cover)->toContain('covers/test.jpg');
});

test('toArray returns array representation', function () {
    $user = User::factory()->create();
    $article = Article::factory()
        ->for($user)
        ->create(['cover' => null, 'is_published' => true]);
    $article->load(['categories', 'user']);

    $card = BlogArticleCard::fromArticle($article);
    $array = $card->toArray();

    expect($array)->toBeArray()
        ->and($array)->toHaveKeys(['title', 'slug', 'excerpt', 'cover', 'categories', 'createdAt', 'createdAtIso', 'authorName']);
});
