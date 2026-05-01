<?php

use App\Services\Clarity\ClarityService;

uses(Tests\TestCase::class);

test('clarity is disabled when the feature flag is off', function () {
    config([
        'clarity.enabled' => false,
        'clarity.project_id' => 'abc123',
    ]);

    expect(ClarityService::isEnabled())->toBeFalse();
});

test('clarity is disabled when project id is empty', function () {
    config([
        'clarity.enabled' => true,
        'clarity.project_id' => '',
    ]);

    expect(ClarityService::isEnabled())->toBeFalse();
});

test('clarity is enabled when both flag and project id are set', function () {
    config([
        'clarity.enabled' => true,
        'clarity.project_id' => 'abc123',
    ]);

    expect(ClarityService::isEnabled())->toBeTrue();
});

test('clarity project id returns the configured value', function () {
    config(['clarity.project_id' => 'p4j5f6']);

    expect(ClarityService::projectId())->toBe('p4j5f6');
});

test('clarity test mode reflects the configured value', function () {
    config(['clarity.test_mode' => true]);
    expect(ClarityService::testEnabled())->toBeTrue();

    config(['clarity.test_mode' => false]);
    expect(ClarityService::testEnabled())->toBeFalse();
});
