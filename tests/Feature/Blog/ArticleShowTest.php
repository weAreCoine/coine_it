<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('returns 200 for a published article', function () {
    $article = Article::factory()
        ->for($this->user)
        ->create(['is_published' => true]);

    $this->get(route('blog.show', $article))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('blog/show'));
});

test('returns 404 for an unpublished article', function () {
    $article = Article::factory()
        ->for($this->user)
        ->create(['is_published' => false]);

    $this->get(route('blog.show', $article))
        ->assertNotFound();
});

test('returns 404 for a non-existent slug', function () {
    $this->get('/blog/non-existent-slug-12345')
        ->assertNotFound();
});

test('returns correct article props', function () {
    $category = Category::factory()->create();
    $tag = Tag::factory()->create();

    $article = Article::factory()
        ->for($this->user)
        ->create([
            'is_published' => true,
            'title' => 'Test Article Title',
            'slug' => 'test-article-title',
            'content' => '<p>Some content here</p>',
            'cover' => null,
        ]);

    $article->categories()->attach($category);
    $article->tags()->attach($tag);

    $this->get(route('blog.show', $article))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('title', 'Test Article Title')
            ->where('slug', 'test-article-title')
            ->has('content')
            ->has('excerpt')
            ->where('cover', null)
            ->where('categories', [['name' => $category->name, 'slug' => $category->slug]])
            ->where('tags', [['name' => $tag->name, 'slug' => $tag->slug]])
            ->where('authorName', $this->user->name)
            ->has('createdAt')
            ->has('createdAtIso')
        );
});

test('seo falls back to title and excerpt when seo fields are null', function () {
    $article = Article::factory()
        ->for($this->user)
        ->create([
            'is_published' => true,
            'seo_title' => null,
            'seo_description' => null,
            'seo_image' => null,
            'cover' => null,
        ]);

    $this->get(route('blog.show', $article))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('seoTitle', $article->title)
            ->where('seoImage', null)
        );
});

test('seo uses custom values when provided', function () {
    $article = Article::factory()
        ->for($this->user)
        ->create([
            'is_published' => true,
            'seo_title' => 'Custom SEO Title',
            'seo_description' => 'Custom SEO Description',
        ]);

    $this->get(route('blog.show', $article))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('seoTitle', 'Custom SEO Title')
            ->where('seoDescription', 'Custom SEO Description')
        );
});

test('returns related articles from same categories, max 3', function () {
    $category = Category::factory()->create();

    $article = Article::factory()
        ->for($this->user)
        ->create(['is_published' => true]);
    $article->categories()->attach($category);

    $related = Article::factory()
        ->count(4)
        ->for($this->user)
        ->create(['is_published' => true]);
    $related->each(fn ($a) => $a->categories()->attach($category));

    $this->get(route('blog.show', $article))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('relatedArticles', 3)
        );
});

test('renders correct OG meta tags in server-side HTML', function () {
    $article = Article::factory()
        ->for($this->user)
        ->create([
            'is_published' => true,
            'seo_title' => 'Custom OG Title',
            'seo_description' => 'Custom OG Description',
        ]);

    $response = $this->get(route('blog.show', $article));

    $response->assertOk()
        ->assertSee('<meta property="og:title" content="Custom OG Title">', false)
        ->assertSee('<meta property="og:description" content="Custom OG Description">', false)
        ->assertSee('<meta property="og:type" content="article">', false);
});

test('related articles exclude unpublished and current article', function () {
    $category = Category::factory()->create();

    $article = Article::factory()
        ->for($this->user)
        ->create(['is_published' => true]);
    $article->categories()->attach($category);

    $unpublished = Article::factory()
        ->for($this->user)
        ->create(['is_published' => false]);
    $unpublished->categories()->attach($category);

    $this->get(route('blog.show', $article))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('relatedArticles', 0)
        );
});
