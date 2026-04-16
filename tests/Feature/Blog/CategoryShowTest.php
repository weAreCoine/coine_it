<?php

use App\Models\Article;
use App\Models\Category;

test('returns 200 and renders blog/category component', function () {
    $category = Category::factory()->create();

    $this->get(route('blog.category', $category))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('blog/category')
            ->where('name', $category->name)
            ->where('slug', $category->slug)
            ->has('articles.data', 0)
        );
});

test('returns published articles belonging to the category', function () {
    $category = Category::factory()->create();
    $published = Article::factory()->count(3)->create(['is_published' => true]);
    $unpublished = Article::factory()->create(['is_published' => false]);

    $category->articles()->attach($published->pluck('id'));
    $category->articles()->attach($unpublished->id);

    $this->get(route('blog.category', $category))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('blog/category')
            ->has('articles.data', 3)
            ->where('articles.data.0.title', $published->sortByDesc('created_at')->first()->title)
        );
});

test('does not return articles from other categories', function () {
    $category = Category::factory()->create();
    $otherCategory = Category::factory()->create();

    $article = Article::factory()->create(['is_published' => true]);
    $otherCategory->articles()->attach($article->id);

    $this->get(route('blog.category', $category))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('articles.data', 0)
        );
});

test('returns 404 for non-existent category slug', function () {
    $this->get('/blog/category/non-existent-slug')
        ->assertNotFound();
});
