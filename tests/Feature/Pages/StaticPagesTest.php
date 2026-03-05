<?php

use Illuminate\Support\Facades\Cache;

test('about page returns 200 and renders about component', function () {
    $this->get(route('about'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('about')
            ->has('numbers', 4)
            ->has('principles', 8)
        );
});

test('contact page returns 200 and renders contact component with faqs', function () {
    $this->get(route('contact.show'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('contact')
            ->has('faqs', 6)
        );
});

test('developing service page returns 200 and renders services/developing', function () {
    Cache::flush();

    $this->get(route('service.developing'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('services/developing')
            ->has('faqs', 6)
            ->has('marquee')
            ->has('cardGrid')
        );
});

test('marketing service page returns 200 and renders services/marketing', function () {
    Cache::flush();

    $this->get(route('service.marketing'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('services/marketing')
            ->has('faqs', 13)
            ->has('marquee')
            ->has('cardGrid')
        );
});

test('content service page returns 200 and renders services/content', function () {
    Cache::flush();

    $this->get(route('service.content'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('services/content')
            ->has('faqs', 12)
            ->has('marquee')
            ->has('cardGrid')
        );
});
