<?php

namespace App\Console\Commands;

use App\Enums\CampaignSubscriptionTypeEnum;
use App\Models\CampaignSubscription;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateSubscriptionDueAmount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-subscription-due-amount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update due amount for campaign subscriptions';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $subscriptions = CampaignSubscription::where('active', true)->get();

        foreach ($subscriptions as $subscription) {
            if ($subscription->subscription_start_date == $subscription->next_donation_date) {
                $subscription->next_donation_date = $this->updateNextDonationDate($subscription);
                $subscription->save();
            }
            if ($subscription->next_donation_date <= now()) {
                $subscription->next_donation_date = $this->updateNextDonationDate($subscription);
                $subscription->due_amount = $subscription->due_amount - $subscription->subscribed_amount;
                $subscription->save();
            }
        }

        $this->info('Due amounts updated successfully.');
    }


    public function updateNextDonationDate(mixed $subscription)
    {
        $nextDonationDate = Carbon::parse($subscription->next_donation_date);

        if ($subscription->subscription_type == CampaignSubscriptionTypeEnum::MONTHLY) {
            return $nextDonationDate->addMonth();
        } else if ($subscription->subscription_type == CampaignSubscriptionTypeEnum::QUARTERLY) {
            return $nextDonationDate->addMonths(3);
        } else if ($subscription->subscription_type == CampaignSubscriptionTypeEnum::HALF_YEARLY) {
            return $nextDonationDate->addMonths(6);
        } else {
            return $nextDonationDate->addYear();
        }
    }
}
