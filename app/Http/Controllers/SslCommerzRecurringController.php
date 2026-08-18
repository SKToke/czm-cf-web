<?php

namespace App\Http\Controllers;

use App\Helpers\FlashHelper;
use App\Models\RecurringSubscription;
use App\Models\RecurringTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SslCommerzRecurringController extends Controller
{
    /**
     * Common payment processor for SSLCommerz success redirect and IPN webhook
     */
    protected function processPaymentValidation(Request $request, string $source = 'IPN')
    {
        Log::channel('sslcommerz')->info("Payment Validation from {$source}", [
            'request' => $request->all()
        ]);

        $tranId = $request->tran_id;
        $status = strtoupper((string)($request->status ?? ''));

        // 1. Locate Transaction & Subscription
        $transaction = null;
        if ($tranId) {
            $transaction = RecurringTransaction::where('tran_id', $tranId)->first();
        }

        $subscription = null;
        if ($transaction) {
            $subscription = $transaction->subscription;
        }

        if (!$subscription && $request->subscription_id) {
            $subscription = RecurringSubscription::where('subscription_id', $request->subscription_id)->first();
        }

        if (!$subscription && $tranId) {
            $subscription = RecurringSubscription::where('last_tran_id', $tranId)->first();
        }

        if (!$subscription) {
            Log::channel('sslcommerz')->error("Subscription not found in {$source}", [
                'tran_id' => $tranId,
                'subscription_id' => $request->subscription_id
            ]);
            return null;
        }

        // 2. Handle VALID / VALIDATED Status
        if (in_array($status, ['VALID', 'VALIDATED', 'SUCCESS'])) {
            $startedAt = $subscription->started_at ?? now();

            // Calculate next billing date
            if ($subscription->next_billing_at && $subscription->status === 'active') {
                $nextBilling = \Carbon\Carbon::parse($subscription->next_billing_at);
                if ($subscription->frequency_type === 'daily') {
                    $nextBilling->addDay();
                } else {
                    $nextBilling->addMonth();
                }
            } else {
                $nextBilling = ($subscription->frequency_type === 'daily')
                    ? now()->addDay()
                    : now()->addMonth();
            }

            // Update Subscription
            $subscription->update([
                'subscription_id' => $request->subscription_id ?? $subscription->subscription_id,
                'last_tran_id' => $tranId ?? $subscription->last_tran_id,
                'status' => 'active',
                'started_at' => $startedAt,
                'next_billing_at' => $nextBilling,
                'last_payment_at' => now(),
                'last_payment_status' => 'valid',
                'bank_tran_id' => $request->bank_tran_id ?? $subscription->bank_tran_id,
                'card_issuer_bank' => $request->card_issuer ?? $subscription->card_issuer_bank,
                'card_no' => $request->card_no ?? $subscription->card_no,
                'card_brand' => $request->card_brand ?? $subscription->card_brand,
                'card_sub_brand' => $request->card_sub_brand ?? $subscription->card_sub_brand,
                'val_id' => $request->val_id ?? $subscription->val_id,
            ]);

            // Update or Create Transaction
            if ($transaction) {
                $transaction->update([
                    'payment_status' => 'valid',
                    'gateway_response' => $request->all(),
                    'paid_at' => $transaction->paid_at ?? now(),
                ]);
            } elseif ($tranId) {
                RecurringTransaction::create([
                    'recurring_subscription_id' => $subscription->id,
                    'tran_id' => $tranId,
                    'amount' => $request->amount ?? $subscription->amount,
                    'currency' => $request->currency ?? $subscription->currency ?? 'BDT',
                    'payment_status' => 'valid',
                    'gateway_response' => $request->all(),
                    'paid_at' => now(),
                ]);
            }

            Log::channel('sslcommerz')->info("Subscription and Transaction marked valid via {$source}", [
                'subscription_id' => $subscription->id,
                'next_billing_at' => $nextBilling,
                'tran_id' => $tranId,
            ]);

            return $subscription;
        }

        // 3. Handle FAILED / CANCELLED Status
        if (in_array($status, ['FAILED', 'CANCELLED', 'UNATTEMPTED', 'EXPIRED'])) {
            if ($transaction && $transaction->payment_status === 'pending') {
                $transaction->update([
                    'payment_status' => strtolower($status),
                    'gateway_response' => $request->all(),
                ]);
            }

            $subscription->update([
                'last_payment_status' => strtolower($status),
            ]);
        }

        return $subscription;
    }

    /**
     * IPN Webhook callback from SSLCommerz
     */
    public function ipn(Request $request)
    {
        $this->processPaymentValidation($request, 'IPN');
        return response()->json(['success' => true]);
    }

    /**
     * Automated billing query for recurring cycles from SSLCommerz engine
     */
    public function billQuery(Request $request)
    {
        Log::channel('sslcommerz')->info('billQuery', [
            'message' => 'Billing Query - Enter',
            'request' => $request->all()
        ]);

        $subscription = RecurringSubscription::where('subscription_id', $request->subscription_id)->first();

        if (!$subscription) {
            Log::channel('sslcommerz')->info('billQuery', [
                'message' => 'Billing Query - Subscription not found',
                'request' => $request->all()
            ]);
            return response()->json([
                'status' => 'FAILED',
                'failedreason' => 'Subscription not found',
                'error_msg_to_display' => 'Subscription not found',
            ]);
        }

        if ($subscription->status !== 'active') {
            Log::channel('sslcommerz')->info('billQuery', [
                'message' => 'Billing Query - subscription status not active',
                'subscription_status' => $subscription->status,
                'request' => $request->all()
            ]);
            return response()->json([
                'status' => 'FAILED',
                'failedreason' => 'Subscription inactive',
                'error_msg_to_display' => 'Subscription inactive',
            ]);
        }

        $tranId = 'TrID' . uniqid();
        $data = [
            'status' => 'success',
            'failedreason' => 'Information okay',
            'error_msg_to_display' => 'Please wait..',
            'tran_id' => $tranId,
            'currency' => 'BDT',
            'amount' => $subscription->amount,
            'refer' => $subscription->refer,
            'subscription_id' => $subscription->subscription_id,
        ];

        RecurringTransaction::create([
            'recurring_subscription_id' => $subscription->id,
            'tran_id' => $tranId,
            'amount' => $subscription->amount,
            'currency' => 'BDT',
            'payment_status' => 'pending',
        ]);

        Log::channel('sslcommerz')->info('billQuery', [
            'message' => 'Billing Query - End',
            'request' => $request->all(),
            'response' => $data,
        ]);

        return response()->json($data);
    }

    /**
     * Customer landing page redirect on success from SSLCommerz
     */
    public function success(Request $request)
    {
        if ($request->has('tran_id') || $request->has('val_id')) {
            $this->processPaymentValidation($request, 'SuccessCallback');
        }

        FlashHelper::trigger('Transaction is successfully completed', 'success');
        return redirect()->route('daily-sadaqah.index', [
            'confirmation' => 'success',
        ]);
    }

    /**
     * Customer landing page redirect on failure from SSLCommerz
     */
    public function fail(Request $request)
    {
        if ($request->has('tran_id')) {
            $this->processPaymentValidation($request, 'FailCallback');
        }

        FlashHelper::trigger('Transaction failed', 'danger');
        return redirect()->route('daily-sadaqah.index');
    }

    /**
     * Customer landing page redirect on cancel from SSLCommerz
     */
    public function cancel(Request $request)
    {
        if ($request->has('tran_id')) {
            $this->processPaymentValidation($request, 'CancelCallback');
        }

        FlashHelper::trigger('Transaction cancelled', 'danger');
        return redirect()->route('daily-sadaqah.index')->with('message', 'Payment was cancelled.');
    }
}
