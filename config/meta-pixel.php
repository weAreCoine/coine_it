<?php

return [
    /*
     * The Meta pixel id, should be a code that looks something like "1202417153106158".
     */
    'pixel_id' => env('META_PIXEL_ID', ''),

    /*
     * The key under which data is saved to the session with flash.
     */
    'session_key' => env('META_PIXEL_SESSION_KEY', config('app.name').'_metaPixel'),

    /*
     * Only if you plan using Conversions API for server events
     * To use the Conversions API, you need an access token. For Documentation please see: https://developers.facebook.com/docs/marketing-api/conversions-api/get-started
     */
    'token' => env('META_PIXEL_TOKEN', ''),

    /*
     * Enable or disable advanced matching. Useful for adjusting user privacy.
     */
    'advanced_matching_enabled' => env('META_PIXEL_ADVANCED_MATCHING_ENABLED', true),

    /*
     * Enable or disable script rendering. Useful for local development.
     */
    'enabled' => env('META_PIXEL_ENABLED', false),

    /*
     * Enable or disable logging. Useful for debugging.
     */
    'logging' => env('META_PIXEL_LOGGING', false),

    /*
     * Test event code for the Conversions API (server events).
     *
     * The library treats the test mode as active when this value is non-empty:
     * every CAPI request is then sent with `test_event_code` and shows up in
     * the Meta Events Manager → Test Events panel instead of going to
     * production. To toggle the behaviour without erasing the code itself we
     * gate the value behind META_TEST_MODE_ENABLED, so flipping a single env
     * flag is enough to switch between test and production.
     */
    'test_event_code' => env('META_TEST_MODE_ENABLED', false)
        ? env('META_TEST_EVENT_CODE')
        : null,
];
