<?php

declare(strict_types=1);

use App\Events\LeadCreated;
use App\Listeners\SyncLeadToKlaviyo;
use App\Models\Lead;
use App\Services\Klaviyo\KlaviyoService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;

it('implements ShouldQueue', function () {
    expect(new SyncLeadToKlaviyo(app(KlaviyoService::class)))
        ->toBeInstanceOf(ShouldQueue::class);
});

it('is registered as a listener for LeadCreated', function () {
    Event::fake();
    Event::assertListening(LeadCreated::class, SyncLeadToKlaviyo::class);
});

it('does not sync when klaviyo is disabled', function () {
    config(['services.klaviyo.enabled' => false]);

    Http::fake();

    $lead = Lead::factory()->withHealthCheck()->create();

    $listener = app(SyncLeadToKlaviyo::class);
    $listener->handle(new LeadCreated($lead));

    Http::assertNothingSent();
});

it('does not sync lead without quiz answers', function () {
    config(['services.klaviyo.enabled' => true, 'services.klaviyo.api_key' => 'test-key']);

    Http::fake();

    $lead = Lead::factory()->create(['quiz_answers' => null]);

    $listener = app(SyncLeadToKlaviyo::class);
    $listener->handle(new LeadCreated($lead));

    Http::assertNothingSent();
});

it('syncs health check lead when klaviyo is enabled', function () {
    config([
        'services.klaviyo.enabled' => true,
        'services.klaviyo.api_key' => 'test-key',
        'services.klaviyo.revision' => '2024-10-15',
    ]);

    Http::fake([
        'a.klaviyo.com/api/profiles/*' => Http::response(['data' => ['id' => 'p-1']], 201),
    ]);

    $lead = Lead::factory()->withHealthCheck()->create();

    $listener = app(SyncLeadToKlaviyo::class);
    $listener->handle(new LeadCreated($lead));

    Http::assertSent(fn ($request) => $request->url() === 'https://a.klaviyo.com/api/profiles/'
        && $request->method() === 'POST'
        && $request['data']['attributes']['email'] === $lead->email);
});
