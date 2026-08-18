<?php

namespace App\Services;

use App\Contracts\RecurringGatewayInterface;
use App\Services\Gateways\BkashRecurringGateway;
use App\Services\Gateways\NagadRecurringGateway;
use App\Services\Gateways\SslCommerzRecurringGateway;
use InvalidArgumentException;

class RecurringGatewayManager
{
    /**
     * Resolve the appropriate gateway driver by name
     *
     * @param string $gateway
     * @return RecurringGatewayInterface
     */
    public function driver(string $gateway): RecurringGatewayInterface
    {
        $normalized = strtolower(trim($gateway));

        return match ($normalized) {
            'bkash' => app(BkashRecurringGateway::class),
            'card', 'sslcommerz', 'ssl' => app(SslCommerzRecurringGateway::class),
            'nagad' => app(NagadRecurringGateway::class),
            default => throw new InvalidArgumentException("Unsupported payment gateway: [{$gateway}]. Supported: bkash, card/sslcommerz, nagad.")
        };
    }
}
