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
    ],

    'recurring' => [
        'sandbox' => [
            'base_url' => env('BKASH_RECURRING_SANDBOX_BASE_URL', 'https://gateway.sbrecurring.pay.bka.sh/'),
            'api_key' => env('BKASH_RECURRING_SANDBOX_API_KEY'),
            'service_id' => env('BKASH_RECURRING_SANDBOX_SERVICE_ID'),
        ],
        'live' => [
            'base_url' => env('BKASH_RECURRING_LIVE_BASE_URL', 'https://gateway.recurring.pay.bka.sh/'),
            'api_key' => env('BKASH_RECURRING_LIVE_API_KEY'),
            'service_id' => env('BKASH_RECURRING_LIVE_SERVICE_ID'),
        ],
    ]
];
