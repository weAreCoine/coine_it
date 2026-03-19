<?php

test('health check page returns 200', function () {
    $this->get('/health-check')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('health-check'));
});

test('health check page has required props', function () {
    $this->get('/health-check')
        ->assertInertia(fn ($page) => $page
            ->has('heroPoints')
            ->has('steps')
            ->has('faqs')
            ->has('questions')
            ->has('teamMembers')
        );
});
