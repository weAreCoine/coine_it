<?php

use App\Services\LinkedIn\LinkedInClient;
use App\Services\LinkedIn\LinkedInService;
use Illuminate\Support\Facades\Http;

// --- LinkedInService unit tests ---

test('isEnabled returns false when disabled', function () {
    config(['linkedin.enabled' => false]);

    expect(LinkedInService::isEnabled())->toBeFalse();
});

test('isEnabled returns true when properly configured', function () {
    config([
        'linkedin.enabled' => true,
        'linkedin.partner_id' => '123456',
        'linkedin.access_token' => 'test-token',
    ]);

    expect(LinkedInService::isEnabled())->toBeTrue();
});

test('isEnabled returns false when partner_id is empty', function () {
    config([
        'linkedin.enabled' => true,
        'linkedin.partner_id' => '',
        'linkedin.access_token' => 'test-token',
    ]);

    expect(LinkedInService::isEnabled())->toBeFalse();
});

test('isEnabled returns false when access_token is empty', function () {
    config([
        'linkedin.enabled' => true,
        'linkedin.partner_id' => '123456',
        'linkedin.access_token' => '',
    ]);

    expect(LinkedInService::isEnabled())->toBeFalse();
});

test('partnerId returns configured value', function () {
    config(['linkedin.partner_id' => '999888']);

    expect(LinkedInService::partnerId())->toBe('999888');
});

test('flashEvent stores event in session', function () {
    config(['linkedin.conversions.lead' => 'urn:lla:llaPartnerConversion:123']);

    LinkedInService::flashEvent('lead');

    $events = session()->get('linkedin_flash_events');

    expect($events)->toHaveCount(1)
        ->and($events[0]['conversionType'])->toBe('lead');
});

test('flashEvent does nothing when conversion URN is empty', function () {
    config(['linkedin.conversions.lead' => '']);

    LinkedInService::flashEvent('lead');

    expect(session()->get('linkedin_flash_events'))->toBeNull();
});

// --- Blade integration tests ---

test('linkedin insight tag is not rendered without consent', function () {
    config([
        'linkedin.enabled' => true,
        'linkedin.partner_id' => '123456',
        'linkedin.access_token' => 'test-token',
    ]);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertDontSee('snap.licdn.com', false);
});

test('linkedin insight tag is rendered with marketing consent', function () {
    config([
        'linkedin.enabled' => true,
        'linkedin.partner_id' => '123456',
        'linkedin.access_token' => 'test-token',
    ]);

    $response = $this->withUnencryptedCookie('cookie_consent', json_encode(['necessary' => true, 'marketing' => true]))
        ->get(route('home'));

    $response->assertOk();
    $response->assertSee('snap.licdn.com', false);
    $response->assertSee('_linkedin_partner_id = "123456"', false);
});

test('linkedin insight tag is not rendered when disabled', function () {
    config([
        'linkedin.enabled' => false,
        'linkedin.partner_id' => '123456',
        'linkedin.access_token' => 'test-token',
    ]);

    $response = $this->withUnencryptedCookie('cookie_consent', json_encode(['necessary' => true, 'marketing' => true]))
        ->get(route('home'));

    $response->assertOk();
    $response->assertDontSee('snap.licdn.com', false);
});

// --- Shared props tests ---

test('shared props include linkedIn data', function () {
    config([
        'linkedin.enabled' => true,
        'linkedin.partner_id' => '123456',
        'linkedin.access_token' => 'test-token',
    ]);

    $this->withUnencryptedCookie('cookie_consent', json_encode(['necessary' => true, 'marketing' => true]))
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('linkedIn')
            ->where('linkedIn.partnerId', '123456')
            ->where('linkedIn.enabled', true)
            ->where('linkedIn.flashEvents', [])
        );
});

test('shared props include linkedIn disabled when not configured', function () {
    config([
        'linkedin.enabled' => false,
        'linkedin.partner_id' => '',
        'linkedin.access_token' => '',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('linkedIn')
            ->where('linkedIn.enabled', false)
        );
});

// --- LinkedInClient HTTP tests ---

test('LinkedInClient sends correct HTTP request', function () {
    Http::fake([
        'api.linkedin.com/rest/conversionEvents' => Http::response(null, 201),
    ]);

    config([
        'linkedin.access_token' => 'test-bearer-token',
        'linkedin.api_version' => '202602',
    ]);

    $client = new LinkedInClient;
    $client->sendConversionEvent(
        'urn:lla:llaPartnerConversion:123',
        [['idType' => 'SHA256_EMAIL', 'idValue' => hash('sha256', 'test@example.com')]],
        [],
        'event-uuid-123',
        1700000000000,
    );

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.linkedin.com/rest/conversionEvents'
            && $request->hasHeader('Authorization', 'Bearer test-bearer-token')
            && $request->hasHeader('Linkedin-Version', '202602')
            && $request->hasHeader('X-Restli-Protocol-Version', '2.0.0')
            && $request['conversion'] === 'urn:lla:llaPartnerConversion:123'
            && $request['eventId'] === 'event-uuid-123'
            && $request['conversionHappenedAt'] === 1700000000000
            && $request['user']['userIds'][0]['idType'] === 'SHA256_EMAIL';
    });
});

// --- trackConversion tests ---

test('trackConversion does not send when consent is missing', function () {
    Http::fake();

    config([
        'linkedin.enabled' => true,
        'linkedin.partner_id' => '123456',
        'linkedin.access_token' => 'test-token',
        'linkedin.conversions.lead' => 'urn:lla:llaPartnerConversion:123',
    ]);

    // No consent cookie
    $this->get(route('home'));

    $request = Request::create('/', 'GET');
    LinkedInService::trackConversion($request, 'lead', 'test@example.com');

    Http::assertNothingSent();
});

test('trackConversion does not send when email is missing', function () {
    Http::fake();

    config([
        'linkedin.enabled' => true,
        'linkedin.partner_id' => '123456',
        'linkedin.access_token' => 'test-token',
        'linkedin.conversions.lead' => 'urn:lla:llaPartnerConversion:123',
    ]);

    $this->withUnencryptedCookie('cookie_consent', json_encode(['necessary' => true, 'marketing' => true]))
        ->get(route('home'));

    $request = Request::create('/', 'GET');
    LinkedInService::trackConversion($request, 'lead');

    Http::assertNothingSent();
});

test('trackConversion sends event with consent and email', function () {
    Http::fake([
        'api.linkedin.com/rest/conversionEvents' => Http::response(null, 201),
    ]);

    config([
        'linkedin.enabled' => true,
        'linkedin.partner_id' => '123456',
        'linkedin.access_token' => 'test-token',
        'linkedin.conversions.lead' => 'urn:lla:llaPartnerConversion:123',
    ]);

    $this->withUnencryptedCookie('cookie_consent', json_encode(['necessary' => true, 'marketing' => true]))
        ->get(route('home'));

    $request = Request::create('/', 'GET');
    LinkedInService::trackConversion($request, 'lead', 'Test@Example.com');

    Http::assertSent(function ($request) {
        $expectedHash = hash('sha256', 'test@example.com');

        return $request['conversion'] === 'urn:lla:llaPartnerConversion:123'
            && $request['user']['userIds'][0]['idValue'] === $expectedHash;
    });
});
