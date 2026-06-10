<?php

namespace App\Http\Controllers;

use App\Helpers\FlashHelper;
use App\Models\Campaign;
use App\Models\RecurringSubscription;
use App\Models\RecurringTransaction;
use App\Models\User;
use App\Services\SslEncryptionHelper;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DailySadaqahController extends Controller
{
    const validation_rules = [
        'payment-amount' => 'required|numeric|min:10',
        'payment-type' => 'required|in:1,2,3',
        'payment-agree' => 'accepted',
        'donor-type' => 'required|in:1,2,3',
    ];
    const guest_validation_rules = [
        'payment-name' => 'required|string|max:255',
        'payment-email' => 'required|email|max:255',
        'payment-phone' => 'nullable|max:15',
    ];

    const validation_messages = [
        'payment-amount.required' => 'Amount is required',
        'payment-amount.numeric' => 'Amount must be a number',
        'payment-amount.min' => 'Amount must be at least :min (BDT)',
        'payment-name.required' => 'Name is required',
        'payment-name.max' => 'Name may not be greater than :max characters',
        'payment-email.required' => 'Email is required',
        'payment-email.email' => 'Please enter a valid email address',
        'payment-email.max' => 'The email may not be greater than :max characters',
        'payment-phone.max' => 'Phone number may not be greater than :max characters',
        'payment-type.required' => 'The donation type is required',
        'payment-type.in' => 'Invalid donation type',
        'payment-agree.accepted' => 'You must agree to the terms and conditions',
        'donor-type.required' => 'The donor type is required',
        'donor-type.in' => 'Invalid donor type',
    ];

    public function index(Request $request): View
    {
        $selectedCampaignId = $request->query('campaign-id');
        $campaign = Campaign::findByCustomId($selectedCampaignId);
        $payableZakat = $request->query('payableZakat');

        return view('daily-sadaqah.index')->with(['campaign' => $campaign, 'payableZakat' => $payableZakat]);
    }

    public function subscribe(Request $request)
    {
        $rules = [
            'payment_amount' => ['required', 'numeric', 'min:1'],
            'frequency' => ['required', 'in:daily,monthly'],
            'payment_method' => ['required'],
        ];
        if (!auth()->check()) {
            $rules['payment_name'] = ['required', 'string', 'max:255'];
            $rules['payment_email'] = ['required', 'email', 'max:255'];
            $rules['payment_phone'] = ['nullable', 'string'];
        }
        $validated = $request->validate($rules);

        if ($request->payment_method === 'bkash') {
            session([
                'payment_origin' => 'daily-sadaqah.index',
                'subscription_amount' => $request->payment_amount,
                'subscription_frequency' => $request->frequency,
            ]);
            return redirect('/bkash/agreement');
        }

        Log::channel('sslcommerz')->info('Subscribe', [
            'message' => 'Subscribe - Enter',
            'request' => $request->all()
        ]);

        if (auth()->check()) {
            $user = auth()->user();
        } else {
            $user = User::where('email', $request->payment_email)->first();
            if (!$user) {
                $user = User::create([
                    'first_name' => $request->payment_name,
                    'email' => $request->payment_email,
                    'mobile_no' => $request->payment_phone,
                    'password' => bcrypt('123456'),
                ]);
            }
        }

        $donor = $user->findOrCreateDonor();

        $refer = config('sslcommerz.' . config('sslcommerz.mode') . '.store_refer');
        $saltKey = config('sslcommerz.' . config('sslcommerz.mode') . '.store_salt_key');
        $storeId = config('sslcommerz.' . config('sslcommerz.mode') . '.store_id');
        $storePass = config('sslcommerz.' . config('sslcommerz.mode') . '.store_password');

        $tranId = 'TrID' . uniqid();

        $subscription = RecurringSubscription::create([
            'donor_id' => $donor->id,
            'refer' => $refer,
            'amount' => $request->payment_amount,
            'currency' => 'BDT',
            'frequency_type' => $request->frequency,
            'status' => 'initiated',
            'last_tran_id' => $tranId,
        ]);

        RecurringTransaction::create([
            'recurring_subscription_id' => $subscription->id,
            'tran_id' => $tranId,
            'amount' => $subscription->amount,
            'currency' => 'BDT',
            'payment_status' => 'pending',
        ]);

        $schedule = json_encode([
            'refer' => $refer,
            'acct_no' => $subscription->id,
            'type' => $request->frequency,
            'dayofmonth' => now()->day,
            'month' => '0',
        ]);

        $encryptedSchedule = SslEncryptionHelper::encrypt($schedule, $saltKey);

        $payload = [
            'store_id' => $storeId,
            'store_passwd' => $storePass,
            'total_amount' => $request->payment_amount,
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
        $data = $response->json();

        if (isset($data['status']) && strtolower($data['status']) === 'success') {
            $subscription->subscription_id = $data['subscription_id'];
            $subscription->subscription_id_onreq = $data['subscription_id'];
            $subscription->subscription_status_onreq = $data['subscription_status'];
            $subscription->sessionkey_onreq = $data['sessionkey'];
            $subscription->save();
        }
        Log::channel('sslcommerz')->info('Subscribe', [
            'message' => 'Subscribe - Exit',
            'payload' => $payload,
            'response' => $data
        ]);

        if (!empty($data['GatewayPageURL'])) {
            return redirect()->away($data['GatewayPageURL']);
        }

        return back()->with('error', 'Unable to start payment gateway.');
    }

    public function ipn(Request $request)
    {
        Log::channel('sslcommerz')->info('IPN', [
            'message' => 'IPN - Enter',
            'request' => $request->all()
        ]);

        $tranId = $request->tran_id;

        // Find transaction FIRST (not subscription)
        $transaction = RecurringTransaction::where('tran_id', $tranId)->first();

        if (!$transaction) {
            Log::channel('sslcommerz')->warning('IPN', [
                'message' => 'IPN - transaction not found',
                'tran_id' => $tranId
            ]);

            // Safety net only — should almost never happen
            $subscription = RecurringSubscription::where('subscription_id', $request->subscription_id)->first();

            if (!$subscription) {
                Log::channel('sslcommerz')->error('IPN', [
                    'message' => 'IPN - Subscription not found',
                    'subscription_id' => $request->subscription_id
                ]);
                return response()->json(['error' => 'Subscription not found'], 404);
            }

            $transaction = RecurringTransaction::create([
                'recurring_subscription_id' => $subscription->id,
                'tran_id' => $tranId,
                'amount' => $subscription->amount,
                'currency' => 'BDT',
                'payment_status' => 'pending',
            ]);
        }

        // Prevent duplicate processing
        if ($transaction->payment_status !== 'pending') {
            Log::channel('sslcommerz')->warning('IPN', [
                'message' => 'IPN - Duplicate',
                'tran_id' => $tranId
            ]);
            return response()->json(['message' => 'Duplicate']);
        }

        $subscription = $transaction->subscription;

        if (!$subscription) {
            Log::channel('sslcommerz')->error('IPN', [
                'message' => 'IPN - Subscription not found',
                'subscription_id' => $transaction->subscription_id
            ]);
            return response()->json(['error' => 'Subscription not found'], 404);
        }

        if ($request->status === 'VALID') {

            $transaction->update([
                'payment_status' => 'valid',
                'gateway_response' => json_encode($request->all()),
                'paid_at' => now(),
            ]);

            $subscription->update([
                'subscription_id' => $request->subscription_id,
                'last_tran_id' => $tranId,
                'last_payment_at' => now(),
                'last_payment_status' => 'valid',
                'status' => 'active',
                'bank_tran_id' => $request->bank_tran_id,
                'card_issuer_bank' => $request->card_issuer,
                'card_no' => $request->card_no,
                'card_brand' => $request->card_brand,
                'card_sub_brand' => $request->card_sub_brand,
                'val_id' => $request->val_id,
            ]);

            if ($subscription->next_billing_at) {
                $nextBilling = $subscription->next_billing_at->copy();
                if ($subscription->frequency_type === 'daily') {
                    $nextBilling->addDay();
                } else {
                    $nextBilling->addMonth();
                }
            } else {
                $nextBilling = $subscription->frequency_type === 'daily' ? now()->addDay() : now()->addMonth();
            }

            $subscription->update([
                'next_billing_at' => $nextBilling
            ]);
        }

        if ($request->status === 'FAILED') {
            $transaction->update([
                'payment_status' => 'failed',
                'gateway_response' => json_encode($request->all()),
                'paid_at' => now(),
            ]);
        }

        Log::channel('sslcommerz')->info('IPN', [
            'message' => 'IPN - End',
            'subscription_id' => $request->subscription_id,
            'tran_id' => $tranId
        ]);

        return response()->json(['success' => true]);
    }

    public function _ipn(Request $request)
    {
        Log::channel('sslcommerz')->info('IPN - Enter', [
            'message' => 'IPN - Enter',
            'request' => $request->all()
        ]);
        $tranId = $request->tran_id;

        // Check duplicate payments
        if (RecurringTransaction::where('tran_id', $tranId)->exists()) {
            Log::channel('sslcommerz')->info('IPN - Duplicate IPN ignored', [
                'message' => 'IPN - Duplicate IPN ignored',
                'request' => $request->all()
            ]);
            return response()->json([
                'message' => 'Duplicate IPN ignored'
            ]);
        }

        $subscription = RecurringSubscription::where('subscription_id', $request->subscription_id)
            ->orWhere('last_tran_id', $tranId)->first();
        if (!$subscription) {
            Log::channel('sslcommerz')->info('IPN - Subscription not found', [
                'message' => 'IPN - Subscription not found',
                'request' => $request->all()
            ]);
            return response()->json(['error' => 'Subscription not found'], 404);
        }

        // Activate subscription
        if ($request->status === 'VALID') {
            $updateData = [
                'subscription_id' => $request->subscription_id,
                'last_tran_id' => $tranId,
                'last_payment_at' => now(),
                'status' => 'active',
                'last_payment_status' => 'valid',

                'bank_tran_id' => $request->bank_tran_id,
                'card_issuer_bank' => $request->card_issuer,
                'card_no' => $request->card_no,
                'card_brand' => $request->card_brand,
                'card_sub_brand' => $request->card_sub_brand,
                'val_id' => $request->val_id,
            ];
            if (!$subscription->started_at) {
                $updateData['started_at'] = now();
            }
            $subscription->update($updateData);

            Log::channel('sslcommerz')->info('IPN - $request->status === VALID', [
                'message' => 'IPN - $request->status === VALID',
                'request' => $request->all(),
                'updateData' => $updateData
            ]);
            // Calculate next billing date
            if ($subscription->next_billing_at) {
                $nextBilling = $subscription->next_billing_at->copy();
                if ($subscription->frequency_type === 'daily') {
                    $nextBilling->addDay();
                } else {
                    $nextBilling->addMonth();
                }
            } else {
                $nextBilling = $subscription->frequency_type === 'daily' ? now()->addDay() : now()->addMonth();
            }
            Log::channel('sslcommerz')->info('IPN - Before update $nextBilling');


            $subscription->update([
                'next_billing_at' => $nextBilling
            ]);

            $transData = [
                'recurring_subscription_id' => $subscription->id,
                'tran_id' => $tranId,
                'amount' => $subscription->amount,
                'currency' => 'BDT',
                'payment_status' => 'valid',
                'gateway_response' => $request->all(),
                'paid_at' => now(),
            ];
            Log::channel('sslcommerz')->info('IPN - Before create', $transData);
            // Store payment history
            $txn = new RecurringTransaction();
            $txn->recurring_subscription_id = $subscription->id;
            $txn->tran_id = $tranId;
            $txn->amount = $subscription->amount;
            $txn->currency = 'BDT';
            $txn->payment_status = 'valid';
            $txn->gateway_response = json_encode($request->all());
            $txn->paid_at = now();
            $txn->save();
//            RecurringTransaction::create($transData);
            Log::channel('sslcommerz')->info('IPN - $request->status === VALID - Create Transaction', [
                'message' => 'IPN - $request->status === VALID - Create Transaction',
                'request' => $request->all(),
                'transData' => $transData
            ]);
        }
        if ($request->status === 'FAILED') {
            $failsData = [
                'recurring_subscription_id' => $subscription->id,
                'tran_id' => $tranId,
                'amount' => $subscription->amount,
                'currency' => 'BDT',
                'payment_status' => 'failed',
                'gateway_response' => $request->all(),
                'paid_at' => now(),
            ];
            Log::channel('sslcommerz')->info('IPN - $request->status === FAILED', [
                'message' => 'IPN - $request->status === FAILED',
                'request' => $request->all(),
                'failsData' => $failsData
            ]);
            RecurringTransaction::create($failsData);
        }
        Log::channel('sslcommerz')->info('IPN - Exit', [
            'message' => 'IPN - Exit',
            'request' => $request->all()
        ]);
        return response()->json(['success' => true]);
    }

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

    public function success(Request $request)
    {
        FlashHelper::trigger('Transaction is successfully completed', 'success');
        return redirect()->route('daily-sadaqah.index', [
            'confirmation' => 'success',
        ]);
    }

    public function fail(Request $request)
    {
        FlashHelper::trigger('Transaction failed', 'danger');
        return redirect()->route('daily-sadaqah.index');
    }

    public function cancel(Request $request)
    {
        FlashHelper::trigger('Transaction cancelled', 'danger');
        return redirect()->route('daily-sadaqah.index')->with('message', 'Payment was cancelled.');
    }
}
