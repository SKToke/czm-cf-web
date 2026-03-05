<?php

namespace App\Http\Controllers\Recurring;

use App\Http\Controllers\Controller;
use App\Models\Recurring\RecurringSubscription;
use App\Models\User;
use App\Services\SslEncryptionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RecurringSubscriptionController extends Controller
{
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


        /*
        |--------------------------------------------------------------------------
        | Call SSLCommerz
        |--------------------------------------------------------------------------
        */

        $baseUrl = config('sslcommerz.' . config('sslcommerz.apiDomain'));

        $response = Http::asForm()->post($baseUrl . config('sslcommerz.apiUrl.make_payment'), $payload);

        $data = $response->json();

        if (!empty($data['GatewayPageURL'])) {
            return redirect()->away($data['GatewayPageURL']);
        }

        return back()->with('error', 'Unable to start payment gateway.');
    }
}
