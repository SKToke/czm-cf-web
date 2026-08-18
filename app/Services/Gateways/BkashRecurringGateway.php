<?php

namespace App\Services\Gateways;

use App\Contracts\RecurringGatewayInterface;
use App\Models\Donor;
use App\Models\RecurringSubscription;
use App\Models\User;
use App\Services\BkashRecurringService;
use Illuminate\Support\Facades\Log;

class BkashRecurringGateway implements RecurringGatewayInterface
{
    protected $bkashService;

    public function __construct(BkashRecurringService $bkashService)
    {
        $this->bkashService = $bkashService;
    }

    /**
     * Initiate bKash recurring checkout
     */
    public function initiateCheckout(array $data, User $user, Donor $donor): string
    {
        session([
            'payment_origin' => 'daily-sadaqah.index',
            'subscription_amount' => $data['payment_amount'],
            'subscription_frequency' => $data['frequency'],
        ]);

        return url('/checkout/bkash-recurring');
    }

    /**
     * Query subscription details from bKash API
     */
    public function querySubscription(RecurringSubscription $subscription): ?array
    {
        try {
            if ($subscription->subscription_id) {
                return $this->bkashService->querySubscription((int)$subscription->subscription_id);
            }
            if ($subscription->last_tran_id) {
                return $this->bkashService->querySubscriptionByRequestId($subscription->last_tran_id);
            }
        } catch (\Exception $e) {
            Log::channel('bkash-recurring')->error('Bkash query subscription failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Cancel bKash subscription via API
     */
    public function cancelSubscription(RecurringSubscription $subscription, string $reason = 'Customer Requested'): bool
    {
        if (!$subscription->subscription_id) {
            return false;
        }

        try {
            $this->bkashService->cancelSubscription((int)$subscription->subscription_id, $reason);
            return true;
        } catch (\Exception $e) {
            Log::channel('bkash-recurring')->error('Bkash cancel subscription failed: ' . $e->getMessage());
            return false;
        }
    }
}
