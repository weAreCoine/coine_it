<?php

use App\Models\Lead;
use App\Services\Klaviyo\KlaviyoService;

test('lead to klaviyo command fails when integration is disabled', function () {
    config([
        'services.klaviyo.enabled' => false,
        'services.klaviyo.api_key' => '',
    ]);

    $lead = Lead::factory()->create();

    $this->artisan("lead:to-klaviyo {$lead->id}")
        ->expectsOutputToContain('Klaviyo integration is disabled')
        ->assertFailed();
});

test('lead to klaviyo command fails when the lead argument is empty', function () {
    config([
        'services.klaviyo.enabled' => true,
        'services.klaviyo.api_key' => 'test-key',
    ]);

    $this->artisan('lead:to-klaviyo', ['lead' => ''])
        ->expectsOutputToContain('Lead ID is required')
        ->assertFailed();
});

test('lead to klaviyo command fails when the lead is not found', function () {
    config([
        'services.klaviyo.enabled' => true,
        'services.klaviyo.api_key' => 'test-key',
    ]);

    $this->artisan('lead:to-klaviyo', ['lead' => 999999])
        ->expectsOutputToContain('Lead ID is not found')
        ->assertFailed();
});

test('lead to klaviyo command syncs the lead when found', function () {
    config([
        'services.klaviyo.enabled' => true,
        'services.klaviyo.api_key' => 'test-key',
    ]);

    $lead = Lead::factory()->create();

    $service = Mockery::mock(KlaviyoService::class);
    $service->shouldReceive('syncLead')
        ->once()
        ->withArgs(fn (Lead $receivedLead) => $receivedLead->is($lead));

    $this->app->instance(KlaviyoService::class, $service);

    $this->artisan("lead:to-klaviyo {$lead->id}")
        ->assertSuccessful();
});
