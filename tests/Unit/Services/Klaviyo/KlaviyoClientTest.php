<?php

declare(strict_types=1);

use App\Services\Klaviyo\KlaviyoClient;
use Illuminate\Support\Facades\Http;

uses(Tests\TestCase::class);

beforeEach(function () {
    config([
        'services.klaviyo.api_key' => 'test-api-key',
        'services.klaviyo.revision' => '2024-10-15',
    ]);
});

it('sends a POST request to create a profile with correct headers and body', function () {
    Http::fake([
        'a.klaviyo.com/api/profiles/*' => Http::response(['data' => ['id' => 'profile-123']], 201),
    ]);

    $client = new KlaviyoClient;
    $client->createProfile([
        'email' => 'test@example.com',
        'first_name' => 'John',
    ]);

    Http::assertSent(function ($request) {
        return $request->url() === 'https://a.klaviyo.com/api/profiles/'
            && $request->method() === 'POST'
            && $request->header('Authorization')[0] === 'Klaviyo-API-Key test-api-key'
            && $request->header('revision')[0] === '2024-10-15'
            && $request->header('Accept')[0] === 'application/vnd.api+json'
            && $request->header('Content-Type')[0] === 'application/vnd.api+json'
            && $request['data']['type'] === 'profile'
            && $request['data']['attributes']['email'] === 'test@example.com';
    });
});

it('sends a POST request to subscribe a profile to a list', function () {
    Http::fake([
        'a.klaviyo.com/api/profile-subscription-bulk-create-jobs*' => Http::response(null, 202),
    ]);

    $client = new KlaviyoClient;
    $client->subscribeToList('test@example.com', 'list-abc');

    Http::assertSent(function ($request) {
        return $request->url() === 'https://a.klaviyo.com/api/profile-subscription-bulk-create-jobs'
            && $request->method() === 'POST'
            && $request->header('Authorization')[0] === 'Klaviyo-API-Key test-api-key'
            && $request['data']['type'] === 'profile-subscription-bulk-create-job'
            && $request['data']['attributes']['profiles']['data'][0]['attributes']['email'] === 'test@example.com'
            && $request['data']['attributes']['profiles']['data'][0]['attributes']['subscriptions']['email']['marketing']['consent'] === 'SUBSCRIBED'
            && $request['data']['relationships']['list']['data']['id'] === 'list-abc';
    });
});

it('sends a GET request to search profile by email', function () {
    Http::fake([
        'a.klaviyo.com/api/profiles?*' => Http::response([
            'data' => [['id' => 'profile-456', 'type' => 'profile']],
        ], 200),
    ]);

    $client = new KlaviyoClient;
    $client->getProfileByEmail('test@example.com');

    Http::assertSent(function ($request) {
        return $request->method() === 'GET'
            && str_contains($request->url(), 'a.klaviyo.com/api/profiles')
            && str_contains($request->url(), 'filter=equals%28email%2C%22test%40example.com%22%29')
            && $request->header('Authorization')[0] === 'Klaviyo-API-Key test-api-key';
    });
});

it('sends a PATCH request to update a profile with correct headers and body', function () {
    Http::fake([
        'a.klaviyo.com/api/profiles/*' => Http::response(['data' => ['id' => 'profile-123']], 200),
    ]);

    $client = new KlaviyoClient;
    $client->updateProfile('profile-123', [
        'first_name' => 'Jane',
    ]);

    Http::assertSent(function ($request) {
        return $request->url() === 'https://a.klaviyo.com/api/profiles/profile-123'
            && $request->method() === 'PATCH'
            && $request['data']['type'] === 'profile'
            && $request['data']['id'] === 'profile-123'
            && $request['data']['attributes']['first_name'] === 'Jane';
    });
});
