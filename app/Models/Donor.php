<?php

namespace App\Models;

use App\Enums\DonorTypeEnum;
use App\Enums\TransactionTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donor extends Model
{
const DONOR_REQUEST_TYPE = [
        'guest' => '1',
        'self' => '2',
        'anonymous' => '3',
    ];
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'user_id', 'donor_type'];

    protected $casts = [
        'donor_type' => DonorTypeEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function successfulDonations()
    {
        return $this->donations()->where('transaction_status', TransactionTypeEnum::Complete->value);
    }

    public function totalDonation(){
        return $this->successfulDonations()->sum('amount');
    }

    public function campaignSubscriptions()
    {
        return $this->hasMany(CampaignSubscription::class,'donor_id');
    }

    public function getCampaignSubscriptions()
    {
        return $this->campaignSubscriptions()->get();
    }

    public function getLastTransactionDateAttribute()
    {
        $lastDonation = $this->successfulDonations()->latest('created_at')->first();
        return $lastDonation ? $lastDonation->created_at : null;
    }

    public function getSuccessfulDonationAmount()
    {
        return $this->successfulDonations()->sum('amount');
    }

    public function getCampaignSpecificSubscription($campaignId)
    {
        $campaignSubscriptions = $this->campaignSubscriptions()
                                ->where('campaign_id', $campaignId)
                                ->where('active', true);

        return $campaignSubscriptions->exists() ? $campaignSubscriptions->first() : null;
    }
}
