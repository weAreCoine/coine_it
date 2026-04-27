<?php

use App\Models\User;
use App\Services\LeadService;
use Combindma\FacebookPixel\Facades\MetaPixel;

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

test('flashed meta pixel events expose the isCustomEvent flag and strip the reserved key', function () {
    $sessionKey = MetaPixel::sessionKey();

    $this->withSession([
        $sessionKey => [
            'Lead' => [
                'data' => [],
                'event_id' => 'evt-lead',
            ],
            'startQuiz' => [
                'data' => [LeadService::META_TRACK_METHOD_KEY => 'trackCustom'],
                'event_id' => 'evt-start',
            ],
        ],
    ])->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('metaPixel.flashEvents', 2)
            ->where('metaPixel.flashEvents.0.eventName', 'Lead')
            ->where('metaPixel.flashEvents.0.isCustomEvent', false)
            ->where('metaPixel.flashEvents.0.data', [])
            ->where('metaPixel.flashEvents.1.eventName', 'startQuiz')
            ->where('metaPixel.flashEvents.1.isCustomEvent', true)
            ->where('metaPixel.flashEvents.1.data', [])
        );
});
