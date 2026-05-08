<?php

use App\Models\CookieConsent;
use Illuminate\Support\Facades\Config;

function consentPayload(array $overrides = []): array
{
    return array_merge([
        'consent_id' => '11111111-2222-4333-8444-555555555555',
        'marketing' => true,
        'analytics' => true,
        'choice_type' => 'accept_all',
        'path' => '/',
    ], $overrides);
}

test('store persists a row with the given choices and returns 204', function () {
    $response = $this->postJson(route('cookie-consent.store'), consentPayload([
        'consent_id' => '22222222-2222-4333-8444-555555555555',
        'marketing' => true,
        'analytics' => false,
        'choice_type' => 'custom',
        'path' => '/contact',
    ]));

    $response->assertNoContent();

    $this->assertDatabaseHas('cookie_consents', [
        'consent_id' => '22222222-2222-4333-8444-555555555555',
        'marketing' => true,
        'analytics' => false,
        'choice_type' => 'custom',
        'path' => '/contact',
        'necessary' => true,
    ]);
});

test('store is idempotent on consent_id (UPSERT)', function () {
    $payload = consentPayload([
        'consent_id' => '33333333-2222-4333-8444-555555555555',
        'marketing' => true,
        'analytics' => true,
        'choice_type' => 'accept_all',
    ]);

    $this->postJson(route('cookie-consent.store'), $payload)->assertNoContent();
    $this->postJson(route('cookie-consent.store'), array_merge($payload, [
        'marketing' => false,
        'choice_type' => 'update',
    ]))->assertNoContent();

    expect(CookieConsent::query()->where('consent_id', $payload['consent_id'])->count())->toBe(1);

    $this->assertDatabaseHas('cookie_consents', [
        'consent_id' => $payload['consent_id'],
        'marketing' => false,
        'choice_type' => 'update',
    ]);
});

test('store hashes the visitor ip with the configured salt', function () {
    Config::set('cookie-consent.ip_salt', 'fixed-test-salt');

    $this->postJson(route('cookie-consent.store'), consentPayload([
        'consent_id' => '44444444-2222-4333-8444-555555555555',
    ]));

    $row = CookieConsent::query()->where('consent_id', '44444444-2222-4333-8444-555555555555')->first();

    expect($row)->not->toBeNull()
        ->and($row->ip_hash)->toBe(hash('sha256', '127.0.0.1fixed-test-salt'));
});

test('store stores a null ip_hash when the salt is empty', function () {
    Config::set('cookie-consent.ip_salt', null);

    $this->postJson(route('cookie-consent.store'), consentPayload([
        'consent_id' => '55555555-2222-4333-8444-555555555555',
    ]));

    $row = CookieConsent::query()->where('consent_id', '55555555-2222-4333-8444-555555555555')->first();

    expect($row->ip_hash)->toBeNull();
});

test('store records the policy version from config', function () {
    Config::set('cookie-consent.version', '2.5');

    $this->postJson(route('cookie-consent.store'), consentPayload([
        'consent_id' => '66666666-2222-4333-8444-555555555555',
    ]));

    $this->assertDatabaseHas('cookie_consents', [
        'consent_id' => '66666666-2222-4333-8444-555555555555',
        'version' => '2.5',
    ]);
});

test('store stores a uuid external_id sourced from the visitor cookie chain', function () {
    // EnsureExternalId middleware ensures every request carries a coine_uid
    // cookie — we only need to verify the controller persists what arrives.
    $this->postJson(route('cookie-consent.store'), consentPayload([
        'consent_id' => '77777777-2222-4333-8444-555555555555',
    ]));

    $row = CookieConsent::query()->where('consent_id', '77777777-2222-4333-8444-555555555555')->first();

    expect($row->external_id)
        ->toBeString()
        ->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i');
});

test('store rejects an invalid choice_type', function () {
    $this->postJson(route('cookie-consent.store'), consentPayload([
        'choice_type' => 'unknown',
    ]))->assertStatus(422)->assertJsonValidationErrors('choice_type');
});

test('store rejects a non-uuid consent_id', function () {
    $this->postJson(route('cookie-consent.store'), consentPayload([
        'consent_id' => 'not-a-uuid',
    ]))->assertStatus(422)->assertJsonValidationErrors('consent_id');
});

test('store rejects when required fields are missing', function () {
    $this->postJson(route('cookie-consent.store'), [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['consent_id', 'marketing', 'analytics', 'choice_type', 'path']);
});

test('store stores the request user_agent and referer', function () {
    $this->withHeaders([
        'User-Agent' => 'TestAgent/1.0',
        'referer' => 'https://example.com/landing',
    ])->postJson(route('cookie-consent.store'), consentPayload([
        'consent_id' => '88888888-2222-4333-8444-555555555555',
    ]));

    $row = CookieConsent::query()->where('consent_id', '88888888-2222-4333-8444-555555555555')->first();

    expect($row->user_agent)->toBe('TestAgent/1.0')
        ->and($row->referer)->toBe('https://example.com/landing');
});
