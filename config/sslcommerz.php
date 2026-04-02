<?php

// SSLCommerz configuration

$apiDomain = env('SSLCZ_TESTMODE') ? "https://sandbox.sslcommerz.com" : "https://securepay.sslcommerz.com";
return [
    'apiCredentials' => [
        'store_id' => env("SSLCZ_STORE_ID"),
        'store_password' => env("SSLCZ_STORE_PASSWORD"),
        'storeLogo' => "https://crowdfunding.czm-bd.org/images/czm_logo.png",
    ],
    'apiUrl' => [
        'make_payment' => "/gwprocess/v4/api.php",
        'check' => "/validator/api/v4/",
        'transaction_status' => "/validator/api/merchantTransIDvalidationAPI.php",
        'order_validate' => "/validator/api/validationserverAPI.php",
        'refund_payment' => "/validator/api/merchantTransIDvalidationAPI.php",
        'refund_status' => "/validator/api/merchantTransIDvalidationAPI.php",
    ],
    'apiDomain' => $apiDomain,
    'connect_from_localhost' => env("IS_LOCALHOST", false), // For Sandbox, use "true", For Live, use "false"
    'success_url' => '/success',
    'failed_url' => '/fail',
    'cancel_url' => '/cancel',
    'ipn_url' => '/ipn',

    //Daily Sadaqah Changes
    'mode' => env('SSLCOMMERZ_MODE', 'sandbox'),
    'sandbox' => [
        'store_id' => env("SSLCZ_SANDBOX_STORE_ID"),
        'store_password' => env("SSLCZ_SANDBOX_STORE_PASSWORD"),
        'store_refer' => env("SSLCZ_SANDBOX_REFER"),
        'store_salt_key' => env("SSLCZ_SANDBOX_SALT_KEY"),
    ],

    'live' => [
        'store_id' => env("SSLCZ_STORE_ID"),
        'store_password' => env("SSLCZ_STORE_PASSWORD"),
        'store_refer' => env("SSLCZ_REFER"),
        'store_salt_key' => env("SSLCZ_SALT_KEY"),
    ]
];
