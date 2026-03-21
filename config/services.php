<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'calendly' => [
        'url' => env('CALENDLY_URL', 'https://calendly.com/luca-coine/30min'),
        'pat' => env('CALENDLY_PAT', ''),
        'webhook_signing_key' => env('CALENDLY_WEBHOOK_SIGNING_KEY', ''),
    ],

    'klaviyo' => [
        'enabled' => (bool) env('KLAVIYO_ENABLED', false),
        'api_key' => env('KLAVIYO_API_KEY', ''),
        'company_id' => env('KLAVIYO_COMPANY_ID', ''),
        'revision' => env('KLAVIYO_API_REVISION', '2024-10-15'),
        'list_id' => env('KLAVIYO_LIST_ID', ''),
    ],

];
