<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\User;
use App\Services\RecurringGatewayManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

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

    public function subscribe(Request $request, RecurringGatewayManager $gatewayManager)
    {
        $rules = [
            'payment_amount' => ['required', 'numeric', 'min:1'],
            'frequency' => ['required', 'in:daily,monthly'],
            'payment_method' => ['required', 'string'],
        ];
        if (!auth()->check()) {
            $rules['payment_name'] = ['required', 'string', 'max:255'];
            $rules['payment_email'] = ['required', 'email', 'max:255'];
            $rules['payment_phone'] = ['nullable', 'string'];
        }
        $validated = $request->validate($rules);

        // 1. Resolve or register user
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
            if ($request->payment_method === 'bkash') {
                auth()->login($user);
            }
        }

        $donor = $user->findOrCreateDonor();

        // 2. Delegate checkout initiation to resolved gateway driver
        try {
            $gateway = $gatewayManager->driver($request->payment_method);
            $redirectUrl = $gateway->initiateCheckout($validated, $user, $donor);

            return redirect()->away($redirectUrl);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage() ?: 'Unable to start payment gateway.');
        }
    }
}
