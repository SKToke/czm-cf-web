<?php

namespace App\Http\Controllers;

use App\Models\RecurringSubscription;
use App\Models\RecurringTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BkashRecurringWebhookController extends Controller
{
    /**
     * Handle incoming bKash Recurring Webhook
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signatureHeader = $request->header('Signature') ?? $request->header('X-Signature');
        $webhookTypeHeader = $request->header('Type') ?? $request->header('type');

        Log::channel('bkash-recurring')->info('Bkash Webhook Received Headers', [
            'Signature' => $signatureHeader,
            'Type' => $webhookTypeHeader,
        ]);

        if (!$signatureHeader) {
            Log::channel('bkash-recurring')->error('Bkash Webhook missing Signature header.');
            return response()->json(['error' => 'Missing Signature'], 400);
        }

        // Validate webhook signature
        $mode = config('bkash.mode', 'sandbox');
        $apiKey = config("bkash.recurring.{$mode}.api_key");

        if (!$apiKey) {
            Log::channel('bkash-recurring')->error('Bkash API Key not configured for mode: ' . $mode);
            return response()->json(['error' => 'Server Configuration Error'], 500);
        }

        try {
            // Decode urlsafe base64 API key
            $key = base64_decode(str_replace(['-', '_'], ['+', '/'], $apiKey));

            // Decode urlsafe base64 Signature
            $signature = base64_decode(str_replace(['-', '_'], ['+', '/'], $signatureHeader));

            // Compute HMAC-SHA256
            $expectedDigest = hash_hmac('sha256', $payload, $key, true);

            if (!hash_equals($expectedDigest, $signature)) {
                Log::channel('bkash-recurring')->error('Bkash Webhook signature validation failed.', [
                    'payload' => $payload,
                    'signature_header' => $signatureHeader
                ]);
                return response()->json(['error' => 'Invalid Signature'], 401);
            }
        } catch (\Exception $e) {
            Log::channel('bkash-recurring')->error('Bkash Webhook signature validation exception: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Error'], 500);
        }

        // Parse JSON payload
        $data = json_decode($payload, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::channel('bkash-recurring')->error('Bkash Webhook invalid JSON payload: ' . json_last_error_msg());
            return response()->json(['error' => 'Invalid JSON'], 400);
        }

        Log::channel('bkash-recurring')->info('Bkash Webhook Signature Validated Successfully', [
            'type' => $webhookTypeHeader,
            'data' => $data
        ]);

        // Process webhook event
        $type = strtolower($webhookTypeHeader ?? $data['type'] ?? '');
        $subscriptionRequestId = $data['subscriptionRequestId'] ?? null;
        $subscriptionId = $data['subscriptionId'] ?? null;

        if (!$subscriptionRequestId && !$subscriptionId) {
            Log::channel('bkash-recurring')->error('Bkash Webhook missing identifiers.', $data);
            return response()->json(['message' => 'Missing Identifiers'], 400);
        }

        // Lookup local subscription
        $subscription = null;
        if ($subscriptionRequestId) {
            $subscription = RecurringSubscription::where('last_tran_id', $subscriptionRequestId)
                ->where('payment_gateway', 'bkash')
                ->first();
        }
        if (!$subscription && $subscriptionId) {
            $subscription = RecurringSubscription::where('subscription_id', $subscriptionId)
                ->where('payment_gateway', 'bkash')
                ->first();
        }

        if (!$subscription) {
            Log::channel('bkash-recurring')->warning('Bkash Webhook subscription not found in local DB.', [
                'subscriptionRequestId' => $subscriptionRequestId,
                'subscriptionId' => $subscriptionId
            ]);
            // Still returning 200 to acknowledge receipt of webhook
            return response()->json(['message' => 'Subscription not found locally'], 200);
        }

        switch ($type) {
            case 'subscription':
                $status = $data['subscriptionStatus'] ?? '';
                if (in_array($status, ['SUCCEEDED', 'VERIFIED', 'ACTIVE'])) {
                    $subscription->update([
                        'status' => 'active',
                        'subscription_id' => $data['subscriptionId'] ?? $subscription->subscription_id,
                        'subscription_status_onreq' => $status,
                        'payer_number' => $data['payer'] ?? $subscription->payer_number,
                        'started_at' => isset($data['timeStamp'])
                            ? now()->parse($data['timeStamp'])
                            : now(),
                        'next_billing_at' => isset($data['nextPaymentDate'])
                            ? now()->parse($data['nextPaymentDate'])
                            : $subscription->next_billing_at,
                        'expires_at' => isset($data['expiryDate'])
                            ? now()->parse($data['expiryDate'])
                            : $subscription->expires_at,
                        'deduction_failure_count' => $data['deductionFailureCount'] ?? $subscription->deduction_failure_count ?? 0,
                    ]);
                } elseif (in_array($status, ['FAILED', 'CANCELLED', 'EXPIRED'])) {
                    $subscription->update([
                        'status' => strtolower($status),
                        'cancelled_by' => $data['cancelledBy'] ?? $subscription->cancelled_by,
                    ]);
                }
                break;

            case 'payment':
                $paymentStatus = $data['paymentStatus'] ?? '';
                $trxId = $data['trxId'] ?? null;

                if ($trxId) {
                    $existingTx = RecurringTransaction::where('tran_id', $trxId)->first();
                    if (!$existingTx) {
                        $isSuccess = ($paymentStatus === 'SUCCEEDED_PAYMENT');
                        $status = $isSuccess ? 'success' : 'failed';

                        RecurringTransaction::create([
                            'recurring_subscription_id' => $subscription->id,
                            'payment_id' => $data['paymentId'] ?? null,
                            'tran_id' => $trxId,
                            'amount' => $data['amount'] ?? $subscription->amount,
                            'currency' => 'BDT',
                            'payment_status' => $status,
                            'paid_at' => now(),
                            'gateway_response' => $data,
                        ]);

                        if ($isSuccess) {
                            $subscription->update([
                                'status' => 'active',
                                'last_payment_at' => now(),
                                'last_payment_status' => 'success',
                                'next_billing_at' => isset($data['nextPaymentDate']) ? now()->parse($data['nextPaymentDate']) : $subscription->next_billing_at,
                            ]);
                        } else {
                            $subscription->update([
                                'last_payment_status' => 'failed',
                                'deduction_failure_count' => ($subscription->deduction_failure_count ?? 0) + 1,
                            ]);
                        }
                    }
                }
                break;

            case 'cancel':
                $subscription->update([
                    'status' => 'cancelled',
                    'cancelled_at' => isset($data['cancelledTime']) ? now()->parse($data['cancelledTime']) : now(),
                    'cancelled_by' => $data['cancelledBy'] ?? 'CUSTOMER',
                ]);
                break;

            case 'expired':
                $subscription->update([
                    'status' => 'expired',
                ]);
                break;

            case 'refund':
                $paymentId = $data['paymentId'] ?? null;
                $reverseTrxId = $data['reverseTrxId'] ?? $data['trxId'] ?? null;
                if ($paymentId) {
                    $tx = RecurringTransaction::where('payment_id', $paymentId)->first();
                    if ($tx) {
                        $tx->update([
                            'payment_status' => 'refunded',
                            'refund_trx_id' => $reverseTrxId,
                            'refunded_amount' => $data['reverseTrxAmount'] ?? $data['amount'] ?? $tx->amount,
                            'refunded_at' => isset($data['reverseTrxTime']) ? now()->parse($data['reverseTrxTime']) : now(),
                            'refund_reason' => $data['reason'] ?? 'bKash Webhook Refund',
                        ]);
                    }
                }
                break;

            default:
                Log::channel('bkash-recurring')->warning('Bkash Webhook unknown type received: ' . $type);
                break;
        }

        return response()->json(['message' => 'success'], 200);
    }
}
