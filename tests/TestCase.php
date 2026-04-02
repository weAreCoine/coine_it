<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.klaviyo.enabled' => false,
            'services.klaviyo.api_key' => '',
            'services.klaviyo.company_id' => '',
            'services.klaviyo.list_id' => '',
            'google-analytics.enabled' => false,
            'google-analytics.measurement_id' => '',
            'google-analytics.api_secret' => '',
            'google-ads.enabled' => false,
            'google-ads.conversion_id' => '',
            'linkedin.enabled' => false,
            'linkedin.partner_id' => '',
            'linkedin.access_token' => '',
            'meta-pixel.enabled' => false,
            'meta-pixel.pixel_id' => '',
            'meta-pixel.token' => '',
        ]);

        Http::preventStrayRequests();
    }
}
