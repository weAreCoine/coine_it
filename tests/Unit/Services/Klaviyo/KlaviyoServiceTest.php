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

it('calls createProfile with correct structure and subscribes to list', function () {
    config(['services.klaviyo.list_id' => 'list-123']);

    Http::fake([
        'a.klaviyo.com/api/profiles/*' => Http::response(['data' => ['id' => 'p-1']], 201),
        'a.klaviyo.com/api/profile-subscription-bulk-create-jobs*' => Http::response(null, 202),
    ]);

    $lead = Lead::factory()->withHealthCheck()->create();

    $service = app(KlaviyoService::class);
    $service->syncLead($lead);

    Http::assertSent(function ($request) use ($lead) {
        $attrs = $request['data']['attributes'] ?? [];

        return $request->url() === 'https://a.klaviyo.com/api/profiles/'
            && $request->method() === 'POST'
            && $attrs['email'] === $lead->email
            && isset($attrs['first_name'])
            && isset($attrs['properties']['quiz_score'])
            && isset($attrs['properties']['quiz_platform'])
            && $attrs['properties']['lead_source'] === 'health_check';
    });

    Http::assertSent(function ($request) use ($lead) {
        return $request->url() === 'https://a.klaviyo.com/api/profile-subscription-bulk-create-jobs'
            && $request->method() === 'POST'
            && $request['data']['attributes']['profiles']['data'][0]['attributes']['email'] === $lead->email
            && $request['data']['relationships']['list']['data']['id'] === 'list-123';
    });
});

it('handles 409 conflict by updating the existing profile and subscribing to list', function () {
    config(['services.klaviyo.list_id' => 'list-123']);

    Http::fake([
        'a.klaviyo.com/api/profiles/' => Http::response([
            'errors' => [[
                'meta' => ['duplicate_profile_id' => 'existing-123'],
            ]],
        ], 409),
        'a.klaviyo.com/api/profiles/existing-123' => Http::response(['data' => ['id' => 'existing-123']], 200),
        'a.klaviyo.com/api/profile-subscription-bulk-create-jobs*' => Http::response(null, 202),
    ]);

    $lead = Lead::factory()->withHealthCheck()->create();

    $service = app(KlaviyoService::class);
    $service->syncLead($lead);

    Http::assertSent(fn ($request) => $request->method() === 'POST'
        && str_contains($request->url(), '/api/profiles/'));
    Http::assertSent(fn ($request) => $request->method() === 'PATCH'
        && str_contains($request->url(), 'existing-123'));
    Http::assertSent(fn ($request) => str_contains($request->url(), 'profile-subscription-bulk-create-jobs'));
});

it('handles exceptions without propagating them', function () {
    Http::fake(fn () => throw new RuntimeException('Connection failed'));

    Log::shouldReceive('error')->atLeast()->once();

    $lead = Lead::factory()->withHealthCheck()->create();

    $service = app(KlaviyoService::class);
    $service->syncLead($lead);
});

it('maps contact form lead with lead_source contact_form', function () {
    $lead = Lead::factory()->create([
        'name' => 'Jane Doe',
        'quiz_answers' => null,
    ]);

    $service = app(KlaviyoService::class);
    $attributes = $service->mapLeadToProfileAttributes($lead);

    expect($attributes)
        ->toHaveKey('first_name', 'Jane')
        ->toHaveKey('last_name', 'Doe')
        ->toHaveKey('email', $lead->email)
        ->and($attributes['properties'])
        ->toHaveKey('lead_source', 'contact_form')
        ->not->toHaveKey('quiz_score')
        ->not->toHaveKey('website')
        ->not->toHaveKey('health_check_call_booked');
});

it('finds profile id by email when profile exists', function () {
    Http::fake([
        'a.klaviyo.com/api/profiles?*' => Http::response([
            'data' => [['id' => 'profile-xyz', 'type' => 'profile']],
        ], 200),
    ]);

    $service = app(KlaviyoService::class);
    $profileId = $service->findProfileIdByEmail('test@example.com');

    expect($profileId)->toBe('profile-xyz');
});

it('returns null when no profile found by email', function () {
    Http::fake([
        'a.klaviyo.com/api/profiles?*' => Http::response(['data' => []], 200),
    ]);

    $service = app(KlaviyoService::class);
    $profileId = $service->findProfileIdByEmail('unknown@example.com');

    expect($profileId)->toBeNull();
});

it('updates profile properties with correct payload', function () {
    Http::fake([
        'a.klaviyo.com/api/profiles/profile-xyz' => Http::response(['data' => ['id' => 'profile-xyz']], 200),
    ]);

    $service = app(KlaviyoService::class);
    $service->updateProfileProperties('profile-xyz', [
        'health_check_call_date' => '2026-04-01T10:00:00.000000Z',
        'health_check_call_url' => 'https://meet.google.com/abc',
    ]);

    Http::assertSent(function ($request) {
        return $request->method() === 'PATCH'
            && str_contains($request->url(), 'profile-xyz')
            && $request['data']['attributes']['properties']['health_check_call_date'] === '2026-04-01T10:00:00.000000Z'
            && $request['data']['attributes']['properties']['health_check_call_url'] === 'https://meet.google.com/abc';
    });
});

it('creates minimal profile and returns id', function () {
    Http::fake([
        'a.klaviyo.com/api/profiles/' => Http::response(['data' => ['id' => 'new-profile-1', 'type' => 'profile']], 201),
    ]);

    $service = app(KlaviyoService::class);
    $profileId = $service->createMinimalProfile('new@example.com');

    expect($profileId)->toBe('new-profile-1');

    Http::assertSent(function ($request) {
        return $request->url() === 'https://a.klaviyo.com/api/profiles/'
            && $request->method() === 'POST'
            && $request['data']['attributes']['email'] === 'new@example.com';
    });
});

it('returns existing profile id on 409 conflict during minimal profile creation', function () {
    Http::fake([
        'a.klaviyo.com/api/profiles/' => Http::response([
            'errors' => [[
                'meta' => ['duplicate_profile_id' => 'existing-456'],
            ]],
        ], 409),
    ]);

    $service = app(KlaviyoService::class);
    $profileId = $service->createMinimalProfile('existing@example.com');

    expect($profileId)->toBe('existing-456');
});

it('returns null when minimal profile creation fails', function () {
    Http::fake([
        'a.klaviyo.com/api/profiles/' => Http::response(['errors' => [['detail' => 'Server error']]], 500),
    ]);

    $service = app(KlaviyoService::class);
    $profileId = $service->createMinimalProfile('fail@example.com');

    expect($profileId)->toBeNull();
});

it('logs warning when update profile properties fails', function () {
    Http::fake([
        'a.klaviyo.com/api/profiles/profile-xyz' => Http::response(['errors' => [['detail' => 'Not found']]], 404),
    ]);

    Log::shouldReceive('warning')
        ->once()
        ->withArgs(fn ($message) => str_contains($message, 'Failed to update Klaviyo profile'));

    $service = app(KlaviyoService::class);
    $service->updateProfileProperties('profile-xyz', [
        'health_check_call_date' => '2026-04-01T10:00:00.000000Z',
    ]);
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
        ->toHaveKey('lead_source', 'health_check')
        ->toHaveKey('health_check_call_booked', 'false')
        ->toHaveKey('health_check_result_url')
        ->and($attributes['properties']['health_check_result_url'])
        ->toContain($lead->uuid);
});
