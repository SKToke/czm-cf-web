<?php

namespace App\Http\Controllers;

use App\Helpers\FlashHelper;
use App\Models\RecurringSubscription;
use App\Models\RecurringTransaction;
use App\Services\BkashRecurringService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserDailySadaqahController extends Controller
{
    public function index()
    {
        $currentUser = auth()->user();
        if (!$currentUser || !$currentUser->active) {
            FlashHelper::trigger('You are not authorized to view this page', 'danger');
            return redirect()->route('home');
        }
        $donor = $currentUser->donor;

        $subscriptions = RecurringSubscription::withSum('transactions', 'amount')
            ->where('donor_id', $donor->id)
            ->latest()
            ->get();

        return view('user-daily-sadaqah.index', compact('subscriptions'));
    }

    public function pause($id)
    {
        $subscription = RecurringSubscription::findOrFail($id);

        if ($subscription->payment_gateway === 'bkash') {
            return back()->with('error', 'Pause is not supported for bKash recurring subscriptions.');
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
        } catch (\Exception $e) {
            Log::channel('sslcommerz')->error('SSLCommerz pause error: ' . $e->getMessage());
        }

        $subscription->update([
            'paused_at' => now(),
            'status' => 'paused'
        ]);

        return back()->with('success', 'Subscription paused successfully.');
    }

    public function resume($id)
    {
        $subscription = RecurringSubscription::findOrFail($id);

        if ($subscription->payment_gateway === 'bkash') {
            return back()->with('error', 'Resume is not supported for bKash recurring subscriptions.');
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
                'action' => 'enableSubscription'
            ];
            $url = config('sslcommerz.apiDomain') . config('sslcommerz.apiUrl.check');
            Http::asForm()->post($url, $payload);
        } catch (\Exception $e) {
            Log::channel('sslcommerz')->error('SSLCommerz resume error: ' . $e->getMessage());
        }

        $subscription->update([
            'resumed_at' => now(),
            'status' => 'active'
        ]);

        return back()->with('success', 'Subscription resumed successfully.');
    }

    public function cancel($id, BkashRecurringService $bkashService)
    {
        $subscription = RecurringSubscription::findOrFail($id);

        if ($subscription->payment_gateway === 'bkash' && $subscription->subscription_id) {
            try {
                $bkashService->cancelSubscription((int)$subscription->subscription_id, 'Customer Requested');
            } catch (\Exception $e) {
                Log::channel('bkash-recurring')->error('bKash customer cancel error: ' . $e->getMessage());
            }
        } elseif ($subscription->payment_gateway === 'sslcommerz' && $subscription->subscription_id) {
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
            } catch (\Exception $e) {
                Log::channel('sslcommerz')->error('SSLCommerz customer cancel error: ' . $e->getMessage());
            }
        }

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now()
        ]);

        return back()->with('success', 'Subscription has been cancelled successfully.');
    }

    public function transactions($id)
    {
        $subscription = RecurringSubscription::findOrFail($id);

        $transactions = RecurringTransaction::where('recurring_subscription_id', $subscription->id)
            ->latest()
            ->get();

        return view('user-daily-sadaqah.transactions', compact('subscription', 'transactions'));
    }
}

