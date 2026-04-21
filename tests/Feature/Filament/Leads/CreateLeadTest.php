<?php

use App\Enums\LeadStage;
use App\Enums\Services;
use App\Filament\Resources\Leads\Pages\CreateLead;
use App\Models\Lead;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('CreateLead accepts valid services enum values and persists them', function () {
    Livewire::test(CreateLead::class)
        ->fillForm([
            'name' => 'Acme Srl',
            'email' => 'hello@acme.test',
            'services' => [
                Services::WebDeveloping->value,
                Services::SEO->value,
            ],
            'stage' => LeadStage::NEW->value,
            'terms' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $lead = Lead::query()->where('email', 'hello@acme.test')->firstOrFail();

    expect($lead->services)->toBe([
        Services::WebDeveloping->value,
        Services::SEO->value,
    ]);
});
