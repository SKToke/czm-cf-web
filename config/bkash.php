<?php
return [
    'mode' => env('BKASH_MODE', 'sandbox'),
    'sandbox' => [
        'base_url' => env("BKASH_SANDBOX_BASE_URL"),
        'callback_url' => env("BKASH_SANDBOX_CALLBACK_URL"),
        'username' => env("BKASH_SANDBOX_USERNAME"),
        'password' => env("BKASH_SANDBOX_PASSWORD"),
        'app_key' => env("BKASH_SANDBOX_APP_KEY"),
        'app_secret' => env("BKASH_SANDBOX_APP_SECRET"),
    ],

    'live' => [
        'base_url' => env("BKASH_LIVE_BASE_URL"),
        'callback_url' => env("BKASH_LIVE_CALLBACK_URL"),
        'username' => env("BKASH_LIVE_USERNAME"),
        'password' => env("BKASH_LIVE_PASSWORD"),
        'app_key' => env("BKASH_LIVE_APP_KEY"),
        'app_secret' => env("BKASH_LIVE_APP_SECRET"),
    ]
];
