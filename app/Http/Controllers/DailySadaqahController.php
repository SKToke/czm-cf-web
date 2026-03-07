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
        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */
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

        /*
        |--------------------------------------------------------------------------
        | Resolve User
        |--------------------------------------------------------------------------
        */
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

        /*
        |--------------------------------------------------------------------------
        | Resolve Donor
        |--------------------------------------------------------------------------
        */
        $donor = $user->findOrCreateDonor();

        /*
        |--------------------------------------------------------------------------
        | Create Subscription
        |--------------------------------------------------------------------------
        */
        $refer = config('sslcommerz.' . config('sslcommerz.mode') . '.store_refer');
        $saltKey = config('sslcommerz.' . config('sslcommerz.mode') . '.store_salt_key');
        $storePass = config('sslcommerz.' . config('sslcommerz.mode') . '.store_password');

        $tranId = 'SUB_' . uniqid();

        $subscription = RecurringSubscription::create([
            'donor_id' => $donor->id,
            'refer' => $refer,
            'amount' => $request->payment_amount,
            'currency' => 'BDT',
            'frequency_type' => $request->frequency,
            'status' => 'initiated',
            'last_tran_id' => $tranId,
        ]);


        /*
        |--------------------------------------------------------------------------
        | SSL Schedule
        |--------------------------------------------------------------------------
        */

        $schedule = json_encode([
            'refer' => $refer,
            'acct_no' => $subscription->id,
            'type' => $request->frequency,
            'dayofmonth' => now()->day,
            'month' => '0',
        ]);

        $encryptedSchedule = SslEncryptionHelper::encrypt($schedule, $saltKey);


        /*
        |--------------------------------------------------------------------------
        | SSL Payload
        |--------------------------------------------------------------------------
        */

        $payload = [

            'store_id' => config('sslcommerz.' . config('sslcommerz.mode') . '.store_id'),
            'store_passwd' => $storePass,

            'total_amount' => $request->payment_amount,
            'currency' => 'BDT',
            'tran_id' => $tranId,

//            'success_url' => route('daily-sadaqah.success'),
//            'fail_url' => route('daily-sadaqah.fail'),
//            'cancel_url' => route('daily-sadaqah.cancel'),
//            'ipn_url' => route('daily-sadaqah.ipn'),
            'success_url' => "https://miles-agravic-autochthonously.ngrok-free.dev/daily-sadaqah-success",
            'fail_url' => "https://miles-agravic-autochthonously.ngrok-free.dev/daily-sadaqah-fail",
            'cancel_url' => "https://miles-agravic-autochthonously.ngrok-free.dev/daily-sadaqah-cancel",
            'ipn_url' => "https://miles-agravic-autochthonously.ngrok-free.dev/daily-sadaqah-ipn",

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


        /*
        |--------------------------------------------------------------------------
        | Call SSLCommerz
        |--------------------------------------------------------------------------
        */

        $response = Http::asForm()->post(config('sslcommerz.apiDomain') . config('sslcommerz.apiUrl.make_payment'), $payload);

        $data = $response->json();

        if (!empty($data['GatewayPageURL'])) {
            return redirect()->away($data['GatewayPageURL']);
        }

        return back()->with('error', 'Unable to start payment gateway.');
    }

    public function ipn(Request $request)
    {
        $tranId = $request->tran_id;

        // Check duplicate payments
        if (RecurringTransaction::where('tran_id', $tranId)->exists()) {
            return response()->json([
                'message' => 'Duplicate IPN ignored'
            ]);
        }

        $subscription = RecurringSubscription::where('last_tran_id', $tranId)->first();
        if (!$subscription) {
            return response()->json(['error' => 'Subscription not found'], 404);
        }

        // Activate subscription
        if ($request->status === 'VALID') {
            $updateData = [
                'subscription_id' => $request->subscription_id ?? $subscription->subscription_id,
                'last_payment_at' => now(),
                'status' => 'active',
                'last_payment_status' => 'valid',
            ];
            if (!$subscription->started_at) {
                $updateData['started_at'] = now();
            }
            $subscription->update($updateData);

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


            $subscription->update([
                'next_billing_at' => $nextBilling
            ]);

            // Store payment history
            RecurringTransaction::create([
                'recurring_subscription_id' => $subscription->id,
                'tran_id' => $tranId,
                'amount' => $subscription->amount,
                'currency' => 'BDT',
                'payment_status' => 'valid',
                'gateway_response' => $request->all(),
                'paid_at' => now(),
            ]);
        }
        return response()->json(['success' => true]);
    }

    public function billQuery(Request $request)
    {
        $subscriptionId = $request->subscription_id;
        $subscription = RecurringSubscription::where('subscription_id', $subscriptionId)->first();

        if (!$subscription) {
            return response()->json([
                'status' => 'FAILED',
                'failedreason' => 'Subscription not found'
            ]);
        }

        /*
        |----------------------------------------------------------
        | Subscription Status Logic
        |----------------------------------------------------------
        */

        if ($subscription->status !== 'active') {
            return response()->json([
                'status' => 'FAILED',
                'failedreason' => 'Subscription inactive'
            ]);
        }

        /*
        |----------------------------------------------------------
        | Allow Charge
        |----------------------------------------------------------
        */

        return response()->json([
            'status' => 'SUCCESS',
            'subscription_id' => $subscription->subscription_id,
            'amount' => $subscription->amount,
            'currency' => 'BDT'
        ]);
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
