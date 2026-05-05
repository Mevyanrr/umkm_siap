<?php

return [

    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // ── Tambahan untuk UMKM SIAP ──────────────────────────────────────────

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
    ],

    'bps' => [
        'api_key' => env('BPS_API_KEY'),
    ],

    'itc_trademap' => [
        'api_key' => env('ITC_TRADEMAP_API_KEY'),
    ],

];
