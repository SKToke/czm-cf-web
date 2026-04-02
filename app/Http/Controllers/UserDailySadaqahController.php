<?php

namespace App\Http\Controllers;

use App\Helpers\FlashHelper;
use App\Models\RecurringSubscription;
use App\Models\RecurringTransaction;
use Illuminate\Support\Facades\Http;

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

        $subscriptions = RecurringSubscription::withSum('transactions', 'amount')->where('donor_id', $donor->id)
            ->latest()
            ->get();

        return view('user-daily-sadaqah.index', compact('subscriptions'));
    }

    public function pause($id)
    {
        $subscription = RecurringSubscription::findOrFail($id);

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
        $response = Http::asForm()->post($url, $payload);
        $data = $response->json();
        dd($url, $payload, $data);


        $subscription->update([
            'paused_at' => now(),
            'status' => 'paused'
        ]);

        return back()->with('success', 'Subscription paused.');
    }

    public function resume($id)
    {
        $subscription = RecurringSubscription::findOrFail($id);

        $subscription->update([
            'resumed_at' => now(),
            'status' => 'active'
        ]);

        return back()->with('success', 'Subscription resumed.');
    }

    public function cancel($id)
    {
        $subscription = RecurringSubscription::findOrFail($id);

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now()
        ]);

        return back()->with('success', 'Subscription is cancelled.');
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
