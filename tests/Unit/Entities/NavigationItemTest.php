<?php

uses(Tests\TestCase::class);

use App\Entities\NavigationItem;

test('isPlaceholder is auto-set when href is #', function () {
    $item = new NavigationItem('Dropdown', '#');

    expect($item->isPlaceholder)->toBeTrue()
        ->and($item->isCurrent)->toBeFalse();
});

test('isPlaceholder is true when explicitly set', function () {
    $item = new NavigationItem('Menu', '#', isPlaceholder: true);

    expect($item->isPlaceholder)->toBeTrue();
});

test('isCurrent is false for external links', function () {
    $item = new NavigationItem('External', 'https://example.com', isExternal: true);

    expect($item->isCurrent)->toBeFalse()
        ->and($item->isExternal)->toBeTrue();
});

test('isCurrent is false for placeholder items', function () {
    $item = new NavigationItem('Placeholder', '#');

    expect($item->isCurrent)->toBeFalse();
});

test('isCurrent is true when request URL matches href', function () {
    $url = url('/');
    $item = new NavigationItem('Home', $url);

    // In test context request URL is http://localhost
    expect($item->isCurrent)->toBeTrue();
});

test('defaults are correct', function () {
    $item = new NavigationItem('Title');

    expect($item->title)->toBe('Title')
        ->and($item->href)->toBe('#')
        ->and($item->isExternal)->toBeFalse()
        ->and($item->targetBlank)->toBeFalse()
        ->and($item->isPlaceholder)->toBeTrue()
        ->and($item->subItems)->toBe([])
        ->and($item->isCallToAction)->toBeFalse();
});

test('subItems can be provided', function () {
    $sub = new NavigationItem('Sub', '/sub');
    $item = new NavigationItem('Parent', '#', subItems: [$sub]);

    expect($item->subItems)->toHaveCount(1)
        ->and($item->subItems[0]->title)->toBe('Sub');
});

test('isCallToAction can be set', function () {
    $item = new NavigationItem('CTA', '/contact', isCallToAction: true);

    expect($item->isCallToAction)->toBeTrue();
});

test('toArray returns array', function () {
    $item = new NavigationItem('Test', '/test');
    $array = $item->toArray();

    expect($array)->toBeArray()
        ->and($array)->toHaveKeys(['title', 'href', 'isExternal', 'targetBlank', 'isPlaceholder', 'subItems', 'isCallToAction', 'isCurrent']);
});
