<?php

use App\Services\GoogleAds\GoogleAdsService;

uses(Tests\TestCase::class);

test('google ads is disabled when the feature flag is off', function () {
    config([
        'google-ads.enabled' => false,
        'google-ads.conversion_id' => 'AW-123456',
    ]);

    expect(GoogleAdsService::isEnabled())->toBeFalse();
});

test('google ads is enabled when configured', function () {
    config([
        'google-ads.enabled' => true,
        'google-ads.conversion_id' => 'AW-123456',
    ]);

    expect(GoogleAdsService::isEnabled())->toBeTrue();
});

test('google ads conversion id returns the configured value', function () {
    config(['google-ads.conversion_id' => 'AW-999999']);

    expect(GoogleAdsService::conversionId())->toBe('AW-999999');
});
