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

it('does not sync lead with newsletter_opt_in false', function () {
    config(['services.klaviyo.enabled' => true, 'services.klaviyo.api_key' => 'test-key']);

    Http::fake();

    $lead = Lead::factory()->create(['newsletter_opt_in' => false, 'quiz_answers' => ['some' => 'data']]);

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

    $lead = Lead::factory()->withHealthCheck()->create(['newsletter_opt_in' => true]);

    $listener = app(SyncLeadToKlaviyo::class);
    $listener->handle(new LeadCreated($lead));

    Http::assertSent(fn ($request) => $request->url() === 'https://a.klaviyo.com/api/profiles/'
        && $request->method() === 'POST'
        && $request['data']['attributes']['email'] === $lead->email);
});

it('syncs contact form lead with newsletter_opt_in true', function () {
    config([
        'services.klaviyo.enabled' => true,
        'services.klaviyo.api_key' => 'test-key',
        'services.klaviyo.revision' => '2024-10-15',
    ]);

    Http::fake([
        'a.klaviyo.com/api/profiles/*' => Http::response(['data' => ['id' => 'p-2']], 201),
    ]);

    $lead = Lead::factory()->create(['newsletter_opt_in' => true, 'quiz_answers' => null]);

    $listener = app(SyncLeadToKlaviyo::class);
    $listener->handle(new LeadCreated($lead));

    Http::assertSent(fn ($request) => $request->url() === 'https://a.klaviyo.com/api/profiles/'
        && $request->method() === 'POST'
        && $request['data']['attributes']['email'] === $lead->email
        && $request['data']['attributes']['properties']['lead_source'] === 'contact_form');
});
