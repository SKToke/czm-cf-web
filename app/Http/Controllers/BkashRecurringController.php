<?php

namespace App\Http\Controllers;

use App\Models\RecurringSubscription;
use App\Models\RecurringTransaction;
use App\Services\BkashRecurringService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BkashRecurringController extends Controller
{
    protected $bkashService;

    public function __construct(BkashRecurringService $bkashService)
    {
        $this->bkashService = $bkashService;
    }

    /**
     * Initiate the recurring subscription checkout
     */
    public function checkout(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $donor = $user->findOrCreateDonor();

        $amount = session('subscription_amount');
        $frequency = session('subscription_frequency');
        $bkashNumber = session('bkash_number');

        if (!$amount || !$frequency || !$bkashNumber) {
            return redirect()->route('daily-sadaqah.index')
                ->with('error_message', 'Invalid donation details. Please try again.');
        }

        // Generate subscriptionRequestId
        $subscriptionRequestId = 'subReq_' . Str::random(8) . '_' . now()->getTimestampMs();

        // Create initial initiated subscription in our DB
        $subscription = RecurringSubscription::create([
            'payment_gateway' => 'bkash',
            'donor_id' => $donor->id,
            'refer' => 'bkash',
            'amount' => $amount,
            'currency' => 'BDT',
            'frequency_type' => $frequency,
            'status' => 'initiated',
            'last_tran_id' => $subscriptionRequestId,
        ]);

        $redirectUrl = route('bkash.recurring.callback');

        // Request bKash subscription creation
        $response = $this->bkashService->createSubscription(
            $subscriptionRequestId,
            (string)$subscription->id,
            (float)$amount,
            $frequency,
            $bkashNumber,
            $redirectUrl
        );

        if (isset($response['redirectURL'])) {
            $subscription->update([
                'subscription_id_onreq' => $subscriptionRequestId,
            ]);

            return redirect()->away($response['redirectURL']);
        }

        // Handle error response from bKash
        $subscription->update([
            'status' => 'failed',
            'last_payment_status' => 'failed',
        ]);

        $errorMessage = $response['errorDescription'] ?? 'Failed to initiate bKash recurring subscription.';
        return redirect()->route('daily-sadaqah.index', ['confirmation' => 'fail'])
            ->with('error_message', $errorMessage);
    }

    /**
     * Customer landing page redirect callback from bKash
     */
    public function callback(Request $request)
    {
        $requestId = $request->query('subscriptionRequestId') ?? $request->query('requestId');
        
        $subscription = null;

        if ($requestId) {
            $subscription = RecurringSubscription::where('last_tran_id', $requestId)
                ->where('payment_gateway', 'bkash')
                ->first();
        }

        if (!$subscription && auth()->check()) {
            // Fallback to the latest initiated subscription for this user
            $user = auth()->user();
            $donor = $user->donor;
            if ($donor) {
                $subscription = RecurringSubscription::where('donor_id', $donor->id)
                    ->where('payment_gateway', 'bkash')
                    ->where('status', 'initiated')
                    ->latest()
                    ->first();
            }
        }

        if (!$subscription) {
            return redirect()->route('daily-sadaqah.index', ['confirmation' => 'fail'])
                ->with('error_message', 'Subscription session not found.');
        }

        // If the webhook already successfully activated it
        if ($subscription->status === 'active') {
            return redirect()->route('daily-sadaqah.index', ['confirmation' => 'success']);
        }

        // Query status from bKash (Fallback since webhook might be asynchronous/delayed)
        $query = $this->bkashService->querySubscriptionByRequestId($subscription->last_tran_id);

        $status = $query['status'] ?? null;

        if (in_array($status, ['SUCCEEDED', 'VERIFIED'])) {
            // Update local subscription to active
            $subscription->update([
                'status' => 'active',
                'subscription_id' => $query['id'] ?? null,
                'subscription_status_onreq' => $status,
                'started_at' => isset($query['createdAt']) ? now()->parse($query['createdAt']) : now(),
                'next_billing_at' => isset($query['nextPaymentDate']) ? now()->parse($query['nextPaymentDate']) : null,
                'last_payment_at' => now(),
                'last_payment_status' => 'success',
            ]);

            // Save first transaction record (if payment was successful)
            $existingTx = RecurringTransaction::where('tran_id', $subscription->last_tran_id)->first();
            if (!$existingTx) {
                RecurringTransaction::create([
                    'recurring_subscription_id' => $subscription->id,
                    'tran_id' => $subscription->last_tran_id,
                    'amount' => $subscription->amount,
                    'currency' => 'BDT',
                    'payment_status' => 'success',
                    'paid_at' => now(),
                    'gateway_response' => $query,
                ]);
            }

            return redirect()->route('daily-sadaqah.index', ['confirmation' => 'success']);
        }

        // Otherwise mark failed
        $subscription->update([
            'status' => 'failed',
            'last_payment_status' => 'failed',
        ]);

        return redirect()->route('daily-sadaqah.index', ['confirmation' => 'fail'])
            ->with('error_message', 'Subscription authorization failed or was cancelled.');
    }
}
