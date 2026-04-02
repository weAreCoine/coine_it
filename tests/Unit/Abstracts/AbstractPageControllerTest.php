<?php

use App\Abstracts\AbstractPageController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

uses(Tests\TestCase::class);

function abstractPageControllerHarness(): object
{
    return new class extends AbstractPageController
    {
        public function loadSvgPublic(string $filename): string
        {
            return $this->loadSvg($filename);
        }
    };
}

test('abstract page controller loads svg files from the public directory', function () {
    File::shouldReceive('get')
        ->once()
        ->with(public_path('svg/icon.svg'))
        ->andReturn('<svg />');

    $controller = abstractPageControllerHarness();

    expect($controller->loadSvgPublic('icon.svg'))->toBe('<svg />');
});

test('abstract page controller returns an empty string and logs when the svg is missing', function () {
    File::shouldReceive('get')
        ->once()
        ->with(public_path('svg/missing.svg'))
        ->andThrow(new RuntimeException('missing svg'));

    Log::shouldReceive('error')
        ->once()
        ->with('missing svg', Mockery::on(function (array $context): bool {
            return $context['exception'] === RuntimeException::class
                && is_string($context['file'])
                && is_int($context['line'])
                && is_array($context['trace']);
        }));

    $controller = abstractPageControllerHarness();

    expect($controller->loadSvgPublic('missing.svg'))->toBe('');
});
