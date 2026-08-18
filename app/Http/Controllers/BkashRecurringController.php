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

        if (!$amount || !$frequency) {
            return redirect()->route('daily-sadaqah.index')
                ->with('error_message', 'Invalid donation details. Please try again.');
        }

        // Generate subscriptionRequestId (strictly alphanumeric, no special characters like underscores)
        $subscriptionRequestId = 'SUBREQ' . Str::random(8) . now()->getTimestampMs();

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

        // Request bKash subscription creation (subscriptionReference must be between 3 and 80 chars)
        $subscriptionReference = 'SUB-' . $subscription->id;

        $response = $this->bkashService->createSubscription(
            $subscriptionRequestId,
            $subscriptionReference,
            (float)$amount,
            $frequency,
            null,
            $redirectUrl
        );

        // Save session request ID to track this specific attempt
        session(['bkash_recurring_request_id' => $subscriptionRequestId]);

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

        $errorMessage = $response['reason'][0]['message'] ?? $response['errorDescription'] ?? 'Failed to initiate bKash recurring subscription.';
        return redirect()->route('daily-sadaqah.index', [
            'confirmation' => 'recurring_fail',
            'invoice' => $subscriptionRequestId,
            'reference' => $subscriptionReference,
        ])->with('error_message', $errorMessage);
    }

    /**
     * Customer landing page redirect callback from bKash
     */
    public function callback(Request $request)
    {
        $requestId = $request->query('subscriptionRequestId') 
            ?? $request->query('requestId') 
            ?? $request->query('subscriptionReference')
            ?? session('bkash_recurring_request_id');
            
        $subscriptionIdParam = $request->query('subscriptionId');
        $callbackStatus = strtolower((string)($request->query('status') ?? $request->query('paymentStatus') ?? $request->query('subscriptionStatus') ?? ''));
        
        $subscription = null;

        if ($requestId) {
            $subscription = RecurringSubscription::where('last_tran_id', $requestId)
                ->where('payment_gateway', 'bkash')
                ->first();

            if (!$subscription && str_starts_with($requestId, 'SUB-')) {
                $subId = (int) str_replace('SUB-', '', $requestId);
                $subscription = RecurringSubscription::find($subId);
            }
        }

        if (!$subscription && $subscriptionIdParam) {
            $subscription = RecurringSubscription::where('subscription_id', $subscriptionIdParam)
                ->where('payment_gateway', 'bkash')
                ->first();
        }

        if (!$subscription && auth()->check()) {
            // Fallback ONLY to latest initiated subscription (never grab an already active one)
            $user = auth()->user();
            $donor = $user->donor ?? $user->findOrCreateDonor();
            if ($donor) {
                $subscription = RecurringSubscription::where('donor_id', $donor->id)
                    ->where('payment_gateway', 'bkash')
                    ->where('status', 'initiated')
                    ->latest()
                    ->first();
            }
        }

        if (!$subscription) {
            return redirect()->route('daily-sadaqah.index', [
                'confirmation' => 'recurring_fail',
                'invoice' => $requestId ?? 'N/A',
                'reference' => 'N/A',
            ])->with('error_message', 'Subscription session not found.');
        }

        $subscriptionRef = 'SUB-' . $subscription->id;

        // 1. Handle deliberate User Cancellation
        if (in_array($callbackStatus, ['cancel', 'cancelled'])) {
            $subscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'last_payment_status' => 'cancelled',
            ]);

            return redirect()->route('daily-sadaqah.index', [
                'confirmation' => 'recurring_cancel',
                'subscriptionId' => $subscription->subscription_id ?? 'N/A',
                'reference' => $subscriptionRef,
                'invoice' => $subscription->last_tran_id,
            ])->with('error_message', 'You have cancelled the subscription process. No amount has been deducted.');
        }

        // 2. Handle Explicit Failure Callback
        if (in_array($callbackStatus, ['failure', 'failed', 'fail'])) {
            $subscription->update([
                'status' => 'failed',
                'last_payment_status' => 'failed',
            ]);

            return redirect()->route('daily-sadaqah.index', [
                'confirmation' => 'recurring_fail',
                'invoice' => $subscription->last_tran_id,
                'reference' => $subscriptionRef,
            ])->with('error_message', 'Subscription authorization failed. Please check your account details or PIN and try again.');
        }

        // 3. Query live status from bKash (Fallback since webhook might be asynchronous/delayed)
        $query = $this->bkashService->querySubscriptionByRequestId($subscription->last_tran_id);

        $status = strtoupper((string)($query['status'] ?? ''));

        if (in_array($status, ['SUCCEEDED', 'VERIFIED'])) {
            $bkashSubId = $query['id'] ?? $subscription->subscription_id;

            // Update local subscription to active
            $subscription->update([
                'status' => 'active',
                'subscription_id' => $bkashSubId,
                'subscription_status_onreq' => $status,
                'payer_number' => $query['payer'] ?? $subscription->payer_number,
                'started_at' => isset($query['createdAt']) ? now()->parse($query['createdAt']) : now(),
                'next_billing_at' => isset($query['nextPaymentDate']) ? now()->parse($query['nextPaymentDate']) : null,
                'expires_at' => isset($query['expiryDate']) ? now()->parse($query['expiryDate']) : $subscription->expires_at,
                'deduction_failure_count' => $query['deductionFailureCount'] ?? 0,
                'last_payment_at' => now(),
                'last_payment_status' => 'success',
            ]);

            // Save first transaction record (if not already recorded by webhook)
            $existingTx = RecurringTransaction::where('recurring_subscription_id', $subscription->id)
                ->where(function ($q) use ($subscription) {
                    $q->where('tran_id', $subscription->last_tran_id)
                      ->orWhereNotNull('payment_id');
                })
                ->first();

            if (!$existingTx) {
                RecurringTransaction::create([
                    'recurring_subscription_id' => $subscription->id,
                    'payment_id' => $query['paymentId'] ?? null,
                    'tran_id' => $subscription->last_tran_id,
                    'amount' => $subscription->amount,
                    'currency' => 'BDT',
                    'payment_status' => 'success',
                    'paid_at' => now(),
                    'gateway_response' => $query,
                ]);
            } else {
                $existingTx->update([
                    'payment_status' => 'success',
                    'gateway_response' => array_merge((array)$existingTx->gateway_response, $query),
                ]);
            }

            return redirect()->route('daily-sadaqah.index', [
                'confirmation' => 'recurring_success',
                'subscriptionId' => $bkashSubId,
                'reference' => $subscriptionRef,
            ]);
        }

        // 4. If bKash gateway query reports CANCELLED
        if ($status === 'CANCELLED') {
            $subscription->update([
                'status' => 'cancelled',
                'cancelled_at' => isset($query['cancelledTime']) ? now()->parse($query['cancelledTime']) : now(),
                'cancelled_by' => $query['cancelledBy'] ?? 'CUSTOMER',
                'last_payment_status' => 'cancelled',
            ]);

            return redirect()->route('daily-sadaqah.index', [
                'confirmation' => 'recurring_cancel',
                'subscriptionId' => $subscription->subscription_id ?? 'N/A',
                'reference' => $subscriptionRef,
                'invoice' => $subscription->last_tran_id,
            ])->with('error_message', 'You have cancelled the subscription process.');
        }

        // 5. Otherwise Authorization failed or PIN was wrong
        $subscription->update([
            'status' => 'failed',
            'last_payment_status' => 'failed',
        ]);

        $failReason = $query['reason'] ?? $query['message'] ?? 'Subscription authorization failed. Please check your account details or PIN and try again.';

        return redirect()->route('daily-sadaqah.index', [
            'confirmation' => 'recurring_fail',
            'invoice' => $subscription->last_tran_id,
            'reference' => $subscriptionRef,
        ])->with('error_message', $failReason);
    }



}
