<?php

uses(Tests\TestCase::class, \Illuminate\Foundation\Testing\RefreshDatabase::class);

use App\Events\LeadCreated;
use Database\Factories\LeadFactory;
use Illuminate\Support\Facades\Event;

test('casts terms as boolean', function () {
    $lead = (new LeadFactory)->create(['terms' => 1]);

    expect($lead->terms)->toBeTrue();
});

test('casts services as json', function () {
    $services = ['WebDeveloping', 'SEO'];
    $lead = (new LeadFactory)->create(['services' => $services]);

    $lead->refresh();

    expect($lead->services)->toBe($services);
});

test('dispatches LeadCreated event on create', function () {
    Event::fake([LeadCreated::class]);

    (new LeadFactory)->create();

    Event::assertDispatched(LeadCreated::class);
});

test('LeadCreated event receives the lead instance', function () {
    Event::fake([LeadCreated::class]);

    $lead = (new LeadFactory)->create();

    Event::assertDispatched(LeadCreated::class, function (LeadCreated $event) use ($lead) {
        return $event->lead->id === $lead->id;
    });
});
