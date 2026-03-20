<?php

use App\Models\Lead;
use App\Services\HealthCheckResultService;

beforeEach(function () {
    $this->service = new HealthCheckResultService;
});

test('buildResultData returns correct structure', function () {
    $lead = Lead::factory()->withHealthCheck()->create();

    $result = $this->service->buildResultData($lead);

    expect($result)
        ->toHaveKeys(['score', 'maxScore', 'rangeColor', 'rangeLabel', 'rangeMessage', 'motivationalTitle', 'motivationalText', 'benchmarkText', 'findings'])
        ->and($result['maxScore'])->toBe(100)
        ->and($result['findings'])->toBeArray();
});

test('range is red for score 0-24', function () {
    $lead = Lead::factory()->withHealthCheck()->create(['quiz_score' => 0]);

    $result = $this->service->buildResultData($lead);

    expect($result['rangeColor'])->toBe('#ef4444');
});

test('range is orange for score 25-49', function () {
    $lead = Lead::factory()->withHealthCheck()->create(['quiz_score' => 25]);

    $result = $this->service->buildResultData($lead);

    expect($result['rangeColor'])->toBe('#f97316');
});

test('range is yellow for score 50-74', function () {
    $lead = Lead::factory()->withHealthCheck()->create(['quiz_score' => 50]);

    $result = $this->service->buildResultData($lead);

    expect($result['rangeColor'])->toBe('#eab308');
});

test('range is green for score 75-100', function () {
    $lead = Lead::factory()->withHealthCheck()->create(['quiz_score' => 75]);

    $result = $this->service->buildResultData($lead);

    expect($result['rangeColor'])->toBe('#22c55e');
});

test('range boundaries are correct', function (int $score, string $expectedColor) {
    $lead = Lead::factory()->withHealthCheck()->create(['quiz_score' => $score]);

    $result = $this->service->buildResultData($lead);

    expect($result['rangeColor'])->toBe($expectedColor);
})->with([
    'min red' => [0, '#ef4444'],
    'max red' => [24, '#ef4444'],
    'min orange' => [25, '#f97316'],
    'max orange' => [49, '#f97316'],
    'min yellow' => [50, '#eab308'],
    'max yellow' => [74, '#eab308'],
    'min green' => [75, '#22c55e'],
    'max green' => [100, '#22c55e'],
]);

test('findings contain negatives for low score answers', function () {
    $lead = Lead::factory()->withHealthCheck()->create([
        'quiz_score' => 3,
        'quiz_answers' => [
            'platform' => ['value' => 'shopify', 'points' => 0],
            'advertising' => ['value' => 'none', 'points' => 3],
            'coordination' => ['value' => 'separate', 'points' => 0],
            'tracking' => ['value' => 'none', 'points' => 0],
            'mobile' => ['value' => 'unknown', 'points' => 0],
            'retention' => ['value' => 'none', 'points' => 0],
        ],
    ]);

    $result = $this->service->buildResultData($lead);

    $colors = array_column($result['findings'], 'color');
    expect($colors)->toContain('r');
});

test('findings contain positives for high score answers', function () {
    $lead = Lead::factory()->withHealthCheck()->create([
        'quiz_score' => 100,
        'quiz_answers' => [
            'platform' => ['value' => 'shopify', 'points' => 0],
            'advertising' => ['value' => 'agency', 'points' => 12],
            'coordination' => ['value' => 'internal', 'points' => 25],
            'tracking' => ['value' => 'complete', 'points' => 25],
            'mobile' => ['value' => 'optimized', 'points' => 20],
            'retention' => ['value' => 'advanced', 'points' => 18],
        ],
    ]);

    $result = $this->service->buildResultData($lead);

    $colors = array_column($result['findings'], 'color');
    expect($colors)->toContain('g')
        ->and($colors)->not->toContain('r');
});

test('findings has max 3 items', function () {
    $lead = Lead::factory()->withHealthCheck()->create();

    $result = $this->service->buildResultData($lead);

    expect(count($result['findings']))->toBeLessThanOrEqual(3);
});
