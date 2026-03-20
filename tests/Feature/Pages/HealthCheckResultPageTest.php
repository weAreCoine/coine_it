<?php

use App\Models\Lead;

test('result page renders for lead with quiz data', function () {
    $lead = Lead::factory()->withHealthCheck()->create();

    $this->get("/health-check/{$lead->uuid}")
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('health-check-result')
            ->has('result.score')
            ->has('result.findings')
            ->has('result.rangeColor')
            ->has('result.rangeLabel')
            ->has('leadName')
        );
});

test('result page returns 404 for lead without quiz', function () {
    $lead = Lead::factory()->create();

    $this->get("/health-check/{$lead->uuid}")
        ->assertNotFound();
});

test('result page returns 404 for invalid uuid', function () {
    $this->get('/health-check/00000000-0000-0000-0000-000000000000')
        ->assertNotFound();
});
