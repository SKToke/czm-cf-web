<?php

namespace App\Http\Controllers;

use App\Helpers\FlashHelper;
use App\Models\Recurring\RecurringSubscription;
use App\Models\Recurring\RecurringTransaction;

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

        $subscriptions = RecurringSubscription::where('donor_id', $donor->id)
            ->latest()
            ->get();

        return view('user-daily-sadaqah.index', compact('subscriptions'));
    }

    public function pause($id)
    {
        $subscription = RecurringSubscription::findOrFail($id);

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
