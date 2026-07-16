<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/success',
        '/cancel',
        '/fail',
        '/ipn',
        '/pay-via-ajax',

        'daily-sadaqah-success',
        'daily-sadaqah-fail',
        'daily-sadaqah-cancel',
        'daily-sadaqah-ipn',
        'daily-sadaqah-bill-query',
        'bkash-recurring/webhook',
    ];
}
