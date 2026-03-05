<?php

uses(Tests\TestCase::class, \Illuminate\Foundation\Testing\RefreshDatabase::class);

use App\Entities\BlogCategoryItem;
use App\Entities\Faq;
use App\Entities\ProjectCategoryItem;
use App\Entities\ProjectStep;
use App\Entities\Service;
use App\Enums\Services;
use App\Models\Category;
use App\Models\ProjectCategory;

test('BlogCategoryItem::fromCategory maps name and slug', function () {
    $category = Category::factory()->create([
        'name' => 'Technology',
        'slug' => 'technology',
    ]);

    $item = BlogCategoryItem::fromCategory($category);

    expect($item->name)->toBe('Technology')
        ->and($item->slug)->toBe('technology');
});

test('BlogCategoryItem::toArray returns correct structure', function () {
    $category = Category::factory()->create();
    $item = BlogCategoryItem::fromCategory($category);

    $array = $item->toArray();
    expect($array)->toHaveKeys(['name', 'slug']);
});

test('ProjectCategoryItem::fromCategory maps name and slug', function () {
    $category = ProjectCategory::forceCreate([
        'name' => 'Design',
        'slug' => 'design',
        'description' => 'Design category',
    ]);

    $item = ProjectCategoryItem::fromCategory($category);

    expect($item->name)->toBe('Design')
        ->and($item->slug)->toBe('design');
});

test('Faq DTO has correct defaults', function () {
    $faq = new Faq('Question?', 'Answer.');

    expect($faq->question)->toBe('Question?')
        ->and($faq->answer)->toBe('Answer.')
        ->and($faq->opened)->toBeFalse();
});

test('Faq DTO accepts opened parameter', function () {
    $faq = new Faq('Q', 'A', true);

    expect($faq->opened)->toBeTrue();
});

test('Faq::toArray returns correct structure', function () {
    $faq = new Faq('Q', 'A');
    $array = $faq->toArray();

    expect($array)->toHaveKeys(['question', 'answer', 'opened']);
});

test('ProjectStep DTO has correct defaults', function () {
    $step = new ProjectStep('Title', 'Description');

    expect($step->title)->toBe('Title')
        ->and($step->description)->toBe('Description')
        ->and($step->assetUrl)->toBeNull()
        ->and($step->number)->toBe(1)
        ->and($step->color)->toBe('');
});

test('ProjectStep DTO accepts all parameters', function () {
    $step = new ProjectStep('Title', 'Desc', '/asset.png', 3, 'blue-500');

    expect($step->assetUrl)->toBe('/asset.png')
        ->and($step->number)->toBe(3)
        ->and($step->color)->toBe('blue-500');
});

test('Service::getServiceLabel returns correct labels for all cases', function () {
    expect(Service::getServiceLabel(Services::Teaching))->toBe('Formazione')
        ->and(Service::getServiceLabel(Services::WebDesign))->toBe('Web Design')
        ->and(Service::getServiceLabel(Services::DigitalMarketing))->toBe('Online Advertising')
        ->and(Service::getServiceLabel(Services::Localization))->toBe('Cultural Localization')
        ->and(Service::getServiceLabel(Services::WebDeveloping))->toBe('Sviluppo Web')
        ->and(Service::getServiceLabel(Services::AppDeveloping))->toBe('Sviluppo App Mobile')
        ->and(Service::getServiceLabel(Services::Content))->toBe('Content Marketing')
        ->and(Service::getServiceLabel(Services::SocialMedia))->toBe('Strategia Social Media')
        ->and(Service::getServiceLabel(Services::MarketingConsulting))->toBe('Marketing Consulting')
        ->and(Service::getServiceLabel(Services::SEO))->toBe('SEO')
        ->and(Service::getServiceLabel(Services::Notion))->toBe('Notion');
});

test('Service::getServiceColor returns correct colors for all cases', function () {
    expect(Service::getServiceColor(Services::Teaching))->toBe('green-500')
        ->and(Service::getServiceColor(Services::WebDesign))->toBe('indigo-500')
        ->and(Service::getServiceColor(Services::DigitalMarketing))->toBe('blue-500')
        ->and(Service::getServiceColor(Services::Localization))->toBe('pink-500')
        ->and(Service::getServiceColor(Services::WebDeveloping))->toBe('amber-500')
        ->and(Service::getServiceColor(Services::AppDeveloping))->toBe('orange-500')
        ->and(Service::getServiceColor(Services::Content))->toBe('red-500')
        ->and(Service::getServiceColor(Services::SocialMedia))->toBe('green-600')
        ->and(Service::getServiceColor(Services::MarketingConsulting))->toBe('slate-600')
        ->and(Service::getServiceColor(Services::SEO))->toBe('pink-700')
        ->and(Service::getServiceColor(Services::Notion))->toBe('black');
});
