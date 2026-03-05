<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('returns 200 and renders blog/index component', function () {
    $this->get(route('blog.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('blog/index'));
});

test('returns max 2 featured published articles', function () {
    Article::factory()
        ->count(3)
        ->for($this->user)
        ->create(['is_published' => true, 'is_featured' => true]);

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('featuredArticles', 2)
        );
});

test('fills featured from non-featured when less than 2 featured exist', function () {
    Article::factory()
        ->for($this->user)
        ->create(['is_published' => true, 'is_featured' => true]);

    Article::factory()
        ->for($this->user)
        ->create(['is_published' => true, 'is_featured' => false]);

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('featuredArticles', 2)
        );
});

test('paginates articles at 6 per page excluding featured', function () {
    Article::factory()
        ->count(10)
        ->for($this->user)
        ->create(['is_published' => true, 'is_featured' => false]);

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('articles.data', 6)
        );
});

test('excludes unpublished articles', function () {
    Article::factory()
        ->for($this->user)
        ->create(['is_published' => false, 'is_featured' => false]);

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('featuredArticles', 0)
            ->has('articles.data', 0)
        );
});

test('filters by category query parameter', function () {
    $category = Category::factory()->create();
    $otherCategory = Category::factory()->create();

    $article = Article::factory()
        ->for($this->user)
        ->create(['is_published' => true, 'is_featured' => false]);
    $article->categories()->attach($category);

    $excluded = Article::factory()
        ->for($this->user)
        ->create(['is_published' => true, 'is_featured' => false]);
    $excluded->categories()->attach($otherCategory);

    $this->get(route('blog.index', ['category' => $category->slug]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('currentCategory', $category->slug)
        );
});

test('returns categories that have at least one published article', function () {
    $usedCategory = Category::factory()->create();
    $unusedCategory = Category::factory()->create();

    $article = Article::factory()
        ->for($this->user)
        ->create(['is_published' => true]);
    $article->categories()->attach($usedCategory);

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('categories', 1)
            ->where('categories.0.slug', $usedCategory->slug)
        );
});

test('returns correct SEO props', function () {
    $this->get(route('blog.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('seoTitle', 'Blog — Coine')
            ->has('seoDescription')
            ->has('canonicalUrl')
        );
});
