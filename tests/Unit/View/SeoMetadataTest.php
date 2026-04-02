<?php

use App\View\Components\SeoMetadata;
use Illuminate\Http\Request;

uses(Tests\TestCase::class);

beforeEach(function () {
    config([
        'app.name' => 'Coiné',
        'app.url' => 'https://coine.test',
    ]);

    $request = Request::create('https://coine.test/seo', 'GET');
    $this->app->instance('request', $request);
    $this->app['url']->setRequest($request);
});

test('seo metadata uses sensible defaults', function () {
    $component = new SeoMetadata;

    expect($component->title)->toBe('Coiné')
        ->and($component->canonicalUrl)->toBe('https://coine.test/seo')
        ->and($component->ogType)->toBe('website')
        ->and($component->ogSiteName)->toBe('Coiné')
        ->and($component->twitterCard)->toBe('summary_large_image')
        ->and($component->article)->toBeNull()
        ->and($component->creativeWork)->toBeNull()
        ->and($component->breadcrumbs)->toBe([])
        ->and($component->organizationSchema()['url'])->toBe('https://coine.test');
});

test('seo metadata builds an article schema and forces article og type', function () {
    $component = new SeoMetadata(
        title: 'Article Title',
        description: 'Article description',
        canonicalUrl: 'https://coine.test/blog/article-title',
        article: [
            'author' => 'Luca',
            'published_time' => '2026-04-01T10:00:00+00:00',
            'modified_time' => '2026-04-02T10:00:00+00:00',
        ],
    );

    $schema = $component->articleSchema();

    expect($component->ogType)->toBe('article')
        ->and($schema)->not->toBeNull()
        ->and($schema['headline'])->toBe('Article Title')
        ->and($schema['author']['name'])->toBe('Luca')
        ->and($schema['datePublished'])->toBe('2026-04-01T10:00:00+00:00')
        ->and($schema['dateModified'])->toBe('2026-04-02T10:00:00+00:00')
        ->and($schema['mainEntityOfPage']['@id'])->toBe('https://coine.test/blog/article-title');
});

test('seo metadata builds a creative work schema when provided', function () {
    $component = new SeoMetadata(
        title: 'Creative Work',
        description: 'Creative description',
        canonicalUrl: 'https://coine.test/projects/creative-work',
        creativeWork: [
            'author' => 'Silvia',
            'published_time' => '2026-03-01T10:00:00+00:00',
        ],
    );

    $schema = $component->creativeWorkSchema();

    expect($schema)->not->toBeNull()
        ->and($schema['@type'])->toBe('CreativeWork')
        ->and($schema['author']['name'])->toBe('Silvia')
        ->and($schema['datePublished'])->toBe('2026-03-01T10:00:00+00:00');
});

test('seo metadata returns null for empty breadcrumbs and builds ordered breadcrumb schema', function () {
    $withoutBreadcrumbs = new SeoMetadata;
    $withBreadcrumbs = new SeoMetadata(
        breadcrumbs: [
            ['name' => 'Home', 'url' => 'https://coine.test'],
            ['name' => 'Blog', 'url' => 'https://coine.test/blog'],
            ['name' => 'Article'],
        ],
    );

    $schema = $withBreadcrumbs->breadcrumbSchema();

    expect($withoutBreadcrumbs->breadcrumbSchema())->toBeNull()
        ->and($schema)->not->toBeNull()
        ->and($schema['itemListElement'][0]['position'])->toBe(1)
        ->and($schema['itemListElement'][1]['name'])->toBe('Blog')
        ->and($schema['itemListElement'][2]['item'])->toBeNull();
});
