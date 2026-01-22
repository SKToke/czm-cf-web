<?php

namespace App\Models;

use App\Enums\CampaignSubscriptionTypeEnum;
use App\Enums\TransactionTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignSubscription extends Model
{
    protected $fillable = [
        'campaign_id',
        'subscription_type',
        'subscribed_amount',
        'last_donated',
        'last_notified',
        'subscription_start_date',
        'next_donation_date',
        'due_amount',
        'donor_id',
        'active'
    ];

    protected $casts = [
        'subscription_type' => CampaignSubscriptionTypeEnum::class,
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function getUserTotalSubscribedDonation()
    {
        $campaign = Campaign::find($this->campaign_id);
        return max(0, $campaign->donations()
                        ->where('donor_id', $this->donor_id)
                        ->where('created_at', '>', $this->created_at)
                        ->where('transaction_status', TransactionTypeEnum::Complete->value)
                        ->sum('amount'));
    }
}
