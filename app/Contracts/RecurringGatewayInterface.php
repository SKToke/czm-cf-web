<?php

namespace App\Contracts;

use App\Models\Donor;
use App\Models\RecurringSubscription;
use App\Models\User;

interface RecurringGatewayInterface
{
    /**
     * Initiate checkout flow for recurring subscription
     *
     * @param array $data Validated input (payment_amount, frequency, etc.)
     * @param User $user
     * @param Donor $donor
     * @return string Redirect URL to send donor to
     */
    public function initiateCheckout(array $data, User $user, Donor $donor): string;

    /**
     * Query subscription details from the gateway
     *
     * @param RecurringSubscription $subscription
     * @return array|null
     */
    public function querySubscription(RecurringSubscription $subscription): ?array;

    /**
     * Cancel a recurring subscription at the gateway level
     *
     * @param RecurringSubscription $subscription
     * @param string $reason
     * @return bool
     */
    public function cancelSubscription(RecurringSubscription $subscription, string $reason = 'Customer Requested'): bool;
}
