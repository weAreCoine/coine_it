<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\Project;

test('sitemap returns xml response', function () {
    $this->get('/sitemap.xml')
        ->assertSuccessful()
        ->assertHeader('Content-Type', 'text/xml; charset=UTF-8');
});

test('sitemap contains static pages', function () {
    $response = $this->get('/sitemap.xml');

    $response->assertSuccessful();
    $content = $response->getContent();

    $staticUrls = [
        '/',
        '/chi-siamo',
        '/contact',
        '/servizi/sviluppo-app-siti-web',
        '/servizi/marketing',
        '/servizi/creazione-contenuti',
        '/progetti',
        '/blog',
        '/privacy-policy',
        '/cookie-policy',
        '/health-check',
    ];

    foreach ($staticUrls as $url) {
        expect($content)->toContain($url);
    }
});

test('sitemap includes published articles', function () {
    $published = Article::factory()->create(['is_published' => true, 'slug' => 'published-article']);

    $content = $this->get('/sitemap.xml')->getContent();

    expect($content)->toContain("/blog/{$published->slug}");
});

test('sitemap excludes unpublished articles', function () {
    $unpublished = Article::factory()->create(['is_published' => false, 'slug' => 'draft-article']);

    $content = $this->get('/sitemap.xml')->getContent();

    expect($content)->not->toContain("/blog/{$unpublished->slug}");
});

test('sitemap includes categories', function () {
    $category = Category::factory()->create(['slug' => 'test-category']);

    $content = $this->get('/sitemap.xml')->getContent();

    expect($content)->toContain("/blog/category/{$category->slug}");
});

test('sitemap includes published projects', function () {
    $published = Project::factory()->create(['is_published' => true, 'slug' => 'published-project']);

    $content = $this->get('/sitemap.xml')->getContent();

    expect($content)->toContain("/progetti/{$published->slug}");
});

test('sitemap excludes unpublished projects', function () {
    $unpublished = Project::factory()->create(['is_published' => false, 'slug' => 'draft-project']);

    $content = $this->get('/sitemap.xml')->getContent();

    expect($content)->not->toContain("/progetti/{$unpublished->slug}");
});
