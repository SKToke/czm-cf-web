<?php

if (!function_exists('bkashErrorMessage')) {
    function bkashErrorMessage($code): string
    {
        return match ($code) {
            '2029' => 'Duplicate transaction. Please try again after some time.',
            '2050' => 'Agreement already exists. Please use existing agreement.',
            '2062' => 'Transaction not found.',
            '2010' => 'Invalid request. Please try again.',
            '2011' => 'Invalid merchant.',
            default => 'Payment failed. Please try again.'
        };
    }
}
