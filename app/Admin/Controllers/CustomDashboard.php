<?php

namespace App\Admin\Controllers;

use App\Enums\TransactionTypeEnum;
use App\Models\Campaign;
use App\Models\CampaignSubscription;
use App\Models\ContactUsQuery;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\NewsletterSubscription;
use App\Models\Nisab;
use App\Models\Notification;
use App\Models\Program;
use App\Models\RecurringSubscription;
use App\Models\RecurringTransaction;
use App\Models\User;
use App\Models\UserZakatCalculation;
use Carbon\Carbon;
use OpenAdmin\Admin\Admin;

class CustomDashboard
{
    public static function title()
    {
        return view('admin.dashboard.title');
    }

    public static function users()
    {
        $users = User::all()->count();

        return Admin::component('admin.dashboard.users', compact('users'));
    }

    public static function donors()
    {
        $donors = Donor::all()->count();

        return Admin::component('admin.dashboard.donors', compact('donors'));
    }

    public static function programs()
    {
        $programs = Program::all()->count();

        return Admin::component('admin.dashboard.programs', compact('programs'));
    }

    public static function campaigns()
    {
        $campaigns = Campaign::all()->count();

        return Admin::component('admin.dashboard.campaigns', compact('campaigns'));
    }

    public static function recurringSubscriptions()
    {
        $subscriptions = RecurringSubscription::where('status', 'active')->count();

        return Admin::component('admin.dashboard.subscriptions', compact('subscriptions'));
    }

    public static function atRiskSubscriptions($times)
    {
        $atRiskSubscriptions = RecurringSubscription::where('status', 'active')
            ->whereHas('transactions', function ($q) {
                $q->where('payment_status', 'failed');
            })
            ->with(['transactions' => function ($q) {
                $q->orderBy('paid_at', 'desc')->limit(3);
            }])
            ->get()
            ->filter(function ($subscription) use ($times) {

                $transactions = $subscription->transactions;

                if ($transactions->count() < $times) {
                    return false;
                }

                return $transactions->every(function ($txn) {
                    return $txn->payment_status === 'failed';
                });

            })->count();

        return Admin::component('admin.dashboard.at-risk-subscriptions', compact('atRiskSubscriptions'));
    }

    public static function todaysUpdates()
    {
        $donations = Donation::where('transaction_status', TransactionTypeEnum::Complete->value)->whereDate('created_at', today())->sum('amount');
        $donors = Donation::where('transaction_status', TransactionTypeEnum::Complete->value)->whereDate('created_at', today())->distinct('donor_id')->count();
        $recurringDonations = RecurringTransaction::where('payment_status', 'valid')->whereDate('paid_at', today())->sum('amount');
        $recurringDonors = RecurringSubscription::whereHas('transactions', function ($query) {
            $query->where('payment_status', 'valid')
                ->whereDate('paid_at', today());
        })->distinct('donor_id')->count('donor_id');
        $campaignSubscriptions = CampaignSubscription::where('active', true)->whereDate('created_at', today())->count();
        $newsletterSubscriptions = NewsletterSubscription::whereDate('created_at', today())->count();
        return Admin::component('admin.dashboard.todays-updates', [
            'donations' => $donations,
            'donors' => $donors,
            'recurringDonations' => $recurringDonations,
            'recurringDonors' => $recurringDonors,
            'campaignSubscriptions' => $campaignSubscriptions,
            'newsletterSubscriptions' => $newsletterSubscriptions,
        ]);
    }

    public static function extraVisitSection()
    {
        $startDate = Carbon::now()->subDays(10)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        $nisab = Nisab::orderBy('nisab_update_date', 'desc')->first();

        $zakatCalculations = UserZakatCalculation::count();
        $goldValue = $nisab ? $nisab->gold_value : '';
        $silverValue = $nisab ? $nisab->silver_value : '';
        $nisabUpdateDate = $nisab ? $nisab->nisab_update_date : '';
        $contactQueries = ContactUsQuery::where('responded', false)->count();
        $notifications = Notification::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->count();
        return Admin::component('admin.dashboard.extra-visit-section', [
            'zakatCalculations' => $zakatCalculations,
            'goldValue' => $goldValue,
            'silverValue' => $silverValue,
            'nisabUpdateDate' => $nisabUpdateDate,
            'contactQueries' => $contactQueries,
            'notifications' => $notifications,
        ]);
    }
}
