<?php

uses(Tests\TestCase::class);

use App\Entities\Service;
use App\Enums\Services;
use Filament\Support\Contracts\HasLabel;

test('Services enum implements Filament HasLabel contract', function () {
    expect(new \ReflectionEnum(Services::class))
        ->implementsInterface(HasLabel::class)->toBeTrue();
});

test('Services::getLabel delegates to Service::getServiceLabel for every case', function (Services $case) {
    expect($case->getLabel())
        ->toBeString()
        ->not->toBeEmpty()
        ->toBe(Service::getServiceLabel($case));
})->with(Services::cases());
