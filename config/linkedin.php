<?php

return [

    /*
    |--------------------------------------------------------------------------
    | LinkedIn Tracking Enabled
    |--------------------------------------------------------------------------
    */
    'enabled' => (bool) env('LINKEDIN_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | LinkedIn Insight Tag Partner ID
    |--------------------------------------------------------------------------
    */
    'partner_id' => env('LINKEDIN_PARTNER_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | LinkedIn Conversions API Access Token
    |--------------------------------------------------------------------------
    */
    'access_token' => env('LINKEDIN_ACCESS_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | LinkedIn API Version (YYYYMM format)
    |--------------------------------------------------------------------------
    */
    'api_version' => env('LINKEDIN_API_VERSION', '202602'),

    /*
    |--------------------------------------------------------------------------
    | Conversion Rule URNs
    |--------------------------------------------------------------------------
    | Format: urn:lla:llaPartnerConversion:XXXXX
    */
    'conversions' => [
        'lead' => env('LINKEDIN_CONVERSION_LEAD_URN', ''),
        'complete_registration' => env('LINKEDIN_CONVERSION_COMPLETE_REGISTRATION_URN', ''),
    ],

];
