<?php

use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

test('returns 200 and renders welcome component', function () {
    Cache::flush();

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('welcome'));
});

test('returns all expected props', function () {
    Cache::flush();

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('hero')
            ->has('marquee')
            ->has('cardGrid')
            ->has('contentStats')
            ->has('ctaBanner')
            ->has('articleGrid')
            ->has('tabSection')
            ->has('featurePromo')
        );
});

test('featurePromo contains expected fields and links to health check', function () {
    Cache::flush();

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('featurePromo.kicker')
            ->has('featurePromo.title')
            ->has('featurePromo.subtitle')
            ->has('featurePromo.image')
            ->has('featurePromo.imageAlt')
            ->has('featurePromo.bullets', 3)
            ->where('featurePromo.theme', 'light')
            ->where('featurePromo.link.href', route('health-check'))
        );
});

test('articleGrid contains max 2 published articles', function () {
    Cache::flush();
    $user = User::factory()->create();

    Article::factory()
        ->count(4)
        ->for($user)
        ->create(['is_published' => true]);

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('articleGrid.articles', 2)
        );
});

test('articleGrid excludes unpublished articles', function () {
    Cache::flush();
    $user = User::factory()->create();

    Article::factory()
        ->for($user)
        ->create(['is_published' => false]);

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('articleGrid.articles', 0)
        );
});

test('marquee slides key exists', function () {
    Cache::flush();

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('marquee.slides')
        );
});

test('hero contains title, description, and link', function () {
    Cache::flush();

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('hero.title')
            ->has('hero.description')
            ->has('hero.link')
        );
});

test('tabSection contains services', function () {
    Cache::flush();

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('tabSection.services', 3)
        );
});
