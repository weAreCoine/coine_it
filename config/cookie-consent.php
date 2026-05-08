<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Policy version
    |--------------------------------------------------------------------------
    |
    | Stored alongside every persisted choice so we can prove which version of
    | the cookie banner / privacy text the user accepted (GDPR Art. 7(1)).
    | Bump manually whenever the banner copy or categories change.
    */
    'version' => env('COOKIE_CONSENT_POLICY_VERSION', '1.0'),

    /*
    |--------------------------------------------------------------------------
    | IP hashing salt
    |--------------------------------------------------------------------------
    |
    | Salt used to hash the visitor IP before persistence. When empty the
    | controller stores `null` instead of a weakly-hashed value (IPv4 is small
    | enough to be brute-forced from an unsalted SHA-256).
    */
    'ip_salt' => env('COOKIE_CONSENT_IP_SALT'),
];
