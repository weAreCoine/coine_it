<?php

declare(strict_types=1);

use App\Models\Lead;
use App\Services\Klaviyo\KlaviyoService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

it('returns false when klaviyo is disabled', function () {
    config(['services.klaviyo.enabled' => false, 'services.klaviyo.api_key' => 'key']);

    expect(KlaviyoService::isEnabled())->toBeFalse();
});

it('returns false when api key is empty', function () {
    config(['services.klaviyo.enabled' => true, 'services.klaviyo.api_key' => '']);

    expect(KlaviyoService::isEnabled())->toBeFalse();
});

it('returns true when enabled and api key is set', function () {
    config(['services.klaviyo.enabled' => true, 'services.klaviyo.api_key' => 'test-key']);

    expect(KlaviyoService::isEnabled())->toBeTrue();
});

it('calls createProfile with correct structure', function () {
    Http::fake([
        'a.klaviyo.com/api/profiles/*' => Http::response(['data' => ['id' => 'p-1']], 201),
    ]);

    $lead = Lead::factory()->withHealthCheck()->create();

    $service = app(KlaviyoService::class);
    $service->syncHealthCheckLead($lead);

    Http::assertSent(function ($request) use ($lead) {
        $attrs = $request['data']['attributes'];

        return $request->url() === 'https://a.klaviyo.com/api/profiles/'
            && $request->method() === 'POST'
            && $attrs['email'] === $lead->email
            && isset($attrs['first_name'])
            && isset($attrs['properties']['quiz_score'])
            && isset($attrs['properties']['quiz_platform'])
            && $attrs['properties']['lead_source'] === 'health_check';
    });
});

it('handles 409 conflict by updating the existing profile', function () {
    Http::fake([
        'a.klaviyo.com/api/profiles/' => Http::response([
            'errors' => [[
                'meta' => ['duplicate_profile_id' => 'existing-123'],
            ]],
        ], 409),
        'a.klaviyo.com/api/profiles/existing-123' => Http::response(['data' => ['id' => 'existing-123']], 200),
    ]);

    $lead = Lead::factory()->withHealthCheck()->create();

    $service = app(KlaviyoService::class);
    $service->syncHealthCheckLead($lead);

    Http::assertSent(fn ($request) => $request->method() === 'POST');
    Http::assertSent(fn ($request) => $request->method() === 'PATCH'
        && str_contains($request->url(), 'existing-123'));
});

it('handles exceptions without propagating them', function () {
    Http::fake(fn () => throw new RuntimeException('Connection failed'));

    Log::shouldReceive('error')->once();

    $lead = Lead::factory()->withHealthCheck()->create();

    $service = app(KlaviyoService::class);
    $service->syncHealthCheckLead($lead);
});

it('maps quiz answers as flattened properties', function () {
    $lead = Lead::factory()->withHealthCheck()->create([
        'name' => 'Mario Rossi',
        'quiz_answers' => [
            'platform' => 'WordPress',
            'advertising' => 'Google Ads',
        ],
        'quiz_score' => 75,
        'website' => 'example.com',
    ]);

    $service = app(KlaviyoService::class);
    $attributes = $service->mapLeadToProfileAttributes($lead);

    expect($attributes)
        ->toHaveKey('first_name', 'Mario')
        ->toHaveKey('last_name', 'Rossi')
        ->toHaveKey('email', $lead->email)
        ->and($attributes['properties'])
        ->toHaveKey('quiz_platform', 'WordPress')
        ->toHaveKey('quiz_advertising', 'Google Ads')
        ->toHaveKey('quiz_score', 75)
        ->toHaveKey('website', 'example.com')
        ->toHaveKey('lead_source', 'health_check');
});
