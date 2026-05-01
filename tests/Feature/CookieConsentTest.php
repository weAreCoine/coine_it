<?php

use App\Helpers\CookieConsent;
use Illuminate\Support\Facades\Mail;

// --- CookieConsent helper tests ---

test('hasMarketingConsent returns false without cookie', function () {
    $this->get(route('home'));

    expect(CookieConsent::hasMarketingConsent())->toBeFalse();
});

test('hasMarketingConsent returns true with marketing true', function () {
    $this->withUnencryptedCookie('cookie_consent', json_encode(['necessary' => true, 'marketing' => true]))
        ->get(route('home'));

    expect(CookieConsent::hasMarketingConsent())->toBeTrue();
});

test('hasMarketingConsent returns false with marketing false', function () {
    $this->withUnencryptedCookie('cookie_consent', json_encode(['necessary' => true, 'marketing' => false]))
        ->get(route('home'));

    expect(CookieConsent::hasMarketingConsent())->toBeFalse();
});

test('hasConsent returns false without cookie', function () {
    $this->get(route('home'));

    expect(CookieConsent::hasConsent())->toBeFalse();
});

test('hasConsent returns true when cookie exists', function () {
    $this->withUnencryptedCookie('cookie_consent', json_encode(['necessary' => true, 'marketing' => false]))
        ->get(route('home'));

    expect(CookieConsent::hasConsent())->toBeTrue();
});

test('getConsent handles malformed JSON gracefully', function () {
    $this->withUnencryptedCookie('cookie_consent', 'not-valid-json')
        ->get(route('home'));

    $consent = CookieConsent::getConsent();

    expect($consent)->toBe(['necessary' => true, 'marketing' => false, 'analytics' => false]);
});

test('hasAnalyticsConsent returns false without cookie', function () {
    $this->get(route('home'));

    expect(CookieConsent::hasAnalyticsConsent())->toBeFalse();
});

test('hasAnalyticsConsent returns true with analytics true', function () {
    $this->withUnencryptedCookie('cookie_consent', json_encode(['necessary' => true, 'marketing' => false, 'analytics' => true]))
        ->get(route('home'));

    expect(CookieConsent::hasAnalyticsConsent())->toBeTrue();
});

test('hasAnalyticsConsent returns false with legacy cookie missing analytics key', function () {
    $this->withUnencryptedCookie('cookie_consent', json_encode(['necessary' => true, 'marketing' => true]))
        ->get(route('home'));

    expect(CookieConsent::hasAnalyticsConsent())->toBeFalse();
});

test('marketing and analytics consent are independent', function () {
    $this->withUnencryptedCookie('cookie_consent', json_encode(['necessary' => true, 'marketing' => true, 'analytics' => false]))
        ->get(route('home'));

    expect(CookieConsent::hasMarketingConsent())->toBeTrue()
        ->and(CookieConsent::hasAnalyticsConsent())->toBeFalse();
});

// --- Blade & Inertia integration tests ---

test('meta pixel is not rendered without consent', function () {
    config([
        'meta-pixel.enabled' => true,
        'meta-pixel.pixel_id' => '123456789',
    ]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertDontSee('fbevents.js', false);
});

test('meta pixel is rendered with marketing consent', function () {
    config([
        'meta-pixel.enabled' => true,
        'meta-pixel.pixel_id' => '123456789',
    ]);

    $response = $this->withUnencryptedCookie('cookie_consent', json_encode(['necessary' => true, 'marketing' => true]))
        ->get(route('home'));

    $response->assertOk();
    $response->assertSee('fbevents.js', false);
});

test('shared props include consent data without cookie', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('consent.given', false)
            ->where('consent.marketing', false)
            ->where('consent.analytics', false)
        );
});

test('shared props include consent data with marketing accepted', function () {
    $this->withUnencryptedCookie('cookie_consent', json_encode(['necessary' => true, 'marketing' => true, 'analytics' => true]))
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('consent.given', true)
            ->where('consent.marketing', true)
            ->where('consent.analytics', true)
        );
});

test('contact form does not track lead event without consent', function () {
    Mail::fake();

    $this->post(route('contact.store'), [
        'firstName' => 'Test',
        'lastName' => 'User',
        'email' => 'test@example.com',
        'phone' => null,
        'message' => 'Project description',
        'termsAccepted' => true,
        'metaEventId' => '11111111-2222-4333-8444-555555555555',
    ])->assertOk();

    $this->assertDatabaseHas('leads', ['email' => 'test@example.com']);
});
