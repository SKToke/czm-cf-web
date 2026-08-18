<?php

namespace App\Services\Gateways;

use App\Contracts\RecurringGatewayInterface;
use App\Models\Donor;
use App\Models\RecurringSubscription;
use App\Models\User;

class NagadRecurringGateway implements RecurringGatewayInterface
{
    /**
     * Initiate Nagad recurring checkout (Placeholder for future Nagad API integration)
     */
    public function initiateCheckout(array $data, User $user, Donor $donor): string
    {
        throw new \BadMethodCallException('Nagad recurring payment gateway is coming soon.');
    }

    /**
     * Query subscription details from Nagad API
     */
    public function querySubscription(RecurringSubscription $subscription): ?array
    {
        throw new \BadMethodCallException('Nagad recurring query is coming soon.');
    }

    /**
     * Cancel Nagad subscription via API
     */
    public function cancelSubscription(RecurringSubscription $subscription, string $reason = 'Customer Requested'): bool
    {
        throw new \BadMethodCallException('Nagad recurring cancel is coming soon.');
    }
}
