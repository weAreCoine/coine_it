<?php

use App\Models\User;

test('shared props include name and env for guest', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('name')
            ->has('env')
            ->where('auth.user', null)
        );
});

test('shared props include authenticated user data', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('auth.user.id', $user->id)
            ->where('auth.user.email', $user->email)
        );
});

test('shared props include navigationItems with 6 items', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('navigationItems', 6)
        );
});

test('Servizi navigation item has subItems', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('navigationItems.2.title', 'Servizi')
            ->where('navigationItems.2.isPlaceholder', true)
            ->has('navigationItems.2.subItems', 3)
        );
});

test('Testa il tuo sito is a call to action', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('navigationItems.5.title', 'Testa il tuo sito')
            ->where('navigationItems.5.isCallToAction', true)
        );
});

test('health check page hides the main navigation', function () {
    $this->get(route('health-check'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('navigationItems', 0)
        );
});

test('shared props expose a per-request meta pixel event id', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('metaPixel.eventId')
            ->has('metaPixel.pixelId')
            ->has('metaPixel.enabled')
            ->missing('metaPixel.flashEvents')
        );
});
