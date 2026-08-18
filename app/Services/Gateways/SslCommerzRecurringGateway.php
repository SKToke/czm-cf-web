<?php

namespace App\Services\Gateways;

use App\Contracts\RecurringGatewayInterface;
use App\Models\Donor;
use App\Models\RecurringSubscription;
use App\Models\RecurringTransaction;
use App\Models\User;
use App\Services\SslEncryptionHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SslCommerzRecurringGateway implements RecurringGatewayInterface
{
    /**
     * Initiate SSLCommerz recurring checkout
     */
    public function initiateCheckout(array $data, User $user, Donor $donor): string
    {
        $refer = config('sslcommerz.' . config('sslcommerz.mode') . '.store_refer');
        $saltKey = config('sslcommerz.' . config('sslcommerz.mode') . '.store_salt_key');
        $storeId = config('sslcommerz.' . config('sslcommerz.mode') . '.store_id');
        $storePass = config('sslcommerz.' . config('sslcommerz.mode') . '.store_password');

        $tranId = 'TrID' . uniqid();

        // 1. Create initiated subscription record
        $subscription = RecurringSubscription::create([
            'payment_gateway' => 'sslcommerz',
            'donor_id' => $donor->id,
            'refer' => $refer,
            'amount' => $data['payment_amount'],
            'currency' => 'BDT',
            'frequency_type' => $data['frequency'],
            'status' => 'initiated',
            'last_tran_id' => $tranId,
        ]);

        // 2. Create pending transaction record
        RecurringTransaction::create([
            'recurring_subscription_id' => $subscription->id,
            'tran_id' => $tranId,
            'amount' => $subscription->amount,
            'currency' => 'BDT',
            'payment_status' => 'pending',
        ]);

        // 3. Encrypt schedule payload
        $schedule = json_encode([
            'refer' => $refer,
            'acct_no' => $subscription->id,
            'type' => $data['frequency'],
            'dayofmonth' => now()->day,
            'month' => '0',
        ]);

        $encryptedSchedule = SslEncryptionHelper::encrypt($schedule, $saltKey);

        $payload = [
            'store_id' => $storeId,
            'store_passwd' => $storePass,
            'total_amount' => $data['payment_amount'],
            'currency' => 'BDT',
            'tran_id' => $tranId,
            'success_url' => route('daily-sadaqah.success'),
            'fail_url' => route('daily-sadaqah.fail'),
            'cancel_url' => route('daily-sadaqah.cancel'),
            'ipn_url' => route('daily-sadaqah.ipn'),
            'cus_name' => $donor->name,
            'cus_email' => $donor->email,
            'cus_add1' => 'Bangladesh',
            'cus_city' => 'Dhaka',
            'cus_country' => 'Bangladesh',
            'cus_phone' => $donor->phone ?? '01700000000',
            'shipping_method' => 'NO',
            'product_name' => 'Daily Sadaqah',
            'product_category' => 'Donation',
            'product_profile' => 'general',
            'schedule' => $encryptedSchedule,
            'multi_card_name' => 'visacard,mastercard',
            'login_req' => 1,
        ];

        $url = config('sslcommerz.apiDomain') . config('sslcommerz.apiUrl.make_payment');
        $response = Http::asForm()->post($url, $payload);
        $resData = $response->json();

        if (isset($resData['status']) && strtolower($resData['status']) === 'success') {
            $subscription->subscription_id = $resData['subscription_id'];
            $subscription->subscription_id_onreq = $resData['subscription_id'];
            $subscription->subscription_status_onreq = $resData['subscription_status'];
            $subscription->sessionkey_onreq = $resData['sessionkey'];
            $subscription->save();
        }

        Log::channel('sslcommerz')->info('SSLCommerz Recurring Subscribe Checkout', [
            'payload' => $payload,
            'response' => $resData
        ]);

        if (!empty($resData['GatewayPageURL'])) {
            return $resData['GatewayPageURL'];
        }

        throw new \RuntimeException($resData['failedreason'] ?? 'Unable to connect to SSLCommerz payment gateway.');
    }

    /**
     * Query subscription details from SSLCommerz API
     */
    public function querySubscription(RecurringSubscription $subscription): ?array
    {
        return [
            'subscription_id' => $subscription->subscription_id,
            'status' => $subscription->status,
        ];
    }

    /**
     * Cancel SSLCommerz subscription via API
     */
    public function cancelSubscription(RecurringSubscription $subscription, string $reason = 'Customer Requested'): bool
    {
        if (!$subscription->subscription_id) {
            return false;
        }

        try {
            $refer = config('sslcommerz.' . config('sslcommerz.mode') . '.store_refer');
            $storeId = config('sslcommerz.' . config('sslcommerz.mode') . '.store_id');
            $storePass = config('sslcommerz.' . config('sslcommerz.mode') . '.store_password');

            $payload = [
                'refer' => $refer,
                'store_id' => $storeId,
                'store_passwd' => $storePass,
                'subscription_id' => $subscription->subscription_id,
                'action' => 'disableSubscription'
            ];
            $url = config('sslcommerz.apiDomain') . config('sslcommerz.apiUrl.check');
            Http::asForm()->post($url, $payload);
            return true;
        } catch (\Exception $e) {
            Log::channel('sslcommerz')->error('SSLCommerz cancel subscription failed: ' . $e->getMessage());
            return false;
        }
    }
}
