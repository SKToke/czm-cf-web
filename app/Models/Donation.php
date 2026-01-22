<?php

namespace App\Models;

use App\Enums\CountryTypeEnum;
use App\Enums\DistrictTypeEnum;
use App\Enums\DonationTypeEnum;
use App\Enums\ReportTypeEnum;
use App\Enums\TransactionTypeEnum;
use App\Services\ReportUpdaterService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use HasFactory;

    protected $casts = [
        'donation_type' => DonationTypeEnum::class,
    ];

    protected $fillable = ['amount', 'donor_id', 'campaign_id', 'transaction_id', 'transaction_status', 'nisab_details', 'donation_type'];

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function getCampaign(): Campaign | null
    {
        return $this->campaign()?->get()?->first();
    }

    public function isGeneralDonation(): bool
    {
        return $this->campaign_id === null;
    }

    public function getThumbnail(): string
    {
        if ($this->isGeneralDonation()){
            return asset('images/image_placeholder.png');
        } else{
            return $this->getCampaign()->getThumbnailImage();
        }
    }

    public function getRaisedAmount(): string
    {
        if ($this->isGeneralDonation()){
            return 'General Purpose';
        } else{
            $totalAmount = $this->campaign->getFundCount();
            $formattedTotalAmount = sprintf("%.2f", $totalAmount); // Format as a float with two decimal places
            return 'BDT ' . $formattedTotalAmount;
        }
    }

    public function getGoalAmount(): string
    {
        if ($this->isGeneralDonation()){
            return 'General Purpose';
        } else{
            return 'BDT ' . $this->campaign->allocated_amount;
        }
    }

    public function getProgramTitle(): string
    {
        if ($this->isGeneralDonation()){
            return "";
        } else{
            return $this->campaign->program->title;
        }
    }

    public function getCampaignTitle(): string
    {
        if ($this->isGeneralDonation()){
            return "";
        } else{
            return $this->campaign->title;
        }
    }

    public function getFundPercentage(): int
    {
        return (int)$this->getCampaign()?->getFundPercentage() ?? 0;
    }

    public function getFormattedDonationDate(): string
    {
        return $this->created_at->format("d M, Y");
    }

    public function getFormattedDonationTime(): string
    {
        return $this->created_at->format("h:i A");
    }

    public function getDonationType(): string
    {
        switch ($this->donation_type) {
            case DonationTypeEnum::ZAKAT:
                return 'Zakat';
            case DonationTypeEnum::SADAKAH_OR_DONATION:
                return 'Sadakah/Donation';
            case DonationTypeEnum::Cash_Waqf:
                return 'Cash Waqf';
            default:
                return 'N/A';
        }
    }

    public function getTransactionStatus(): string
    {
        switch ($this->transaction_status) {
            case TransactionTypeEnum::Pending->value:
                return 'Pending';
            case TransactionTypeEnum::Complete->value:
                return 'Success';
            case TransactionTypeEnum::Failed->value:
                return 'Failed';
            case TransactionTypeEnum::Canceled->value:
                return 'Canceled';
            default:
                return 'N/A';
        }
    }

    public function updateSubscription()
    {
        $subscription = CampaignSubscription::where('campaign_id', $this->campaign_id)
                                            ->where('donor_id', $this->donor_id)
                                            ->where('active', true)
                                            ->first();
        if($subscription) {
            $subscription->due_amount += $this->amount;
            $subscription->last_donated = $this->created_at;
            $subscription->save();
        }
        return $subscription;
    }

    public static function boot()
    {
        parent::boot();

        static::created(function ($donation) {
            /**
             * After create, check if any subscription exists for this donor.
             * If found update subscription info.
             */
            if ($donation->campaign_id != null) {
                $donation->updateSubscription();
            }
        });
    }

    public function setCampaign($campaignId): void
    {
        $campaign = Campaign::findByCustomId($campaignId);
        $this->campaign_id = $campaign->id;
        $this->save();
    }

    public function updateOrCreateReport()
    {
        $donor = $this->donor;

        if (!$donor) {
            return;
        }

        $user = $donor->user;

        $reportEntry = [
            'ID' => $donor->id,
            'Name' => $donor->name,
            'Mobile' => $donor->phone,
            'Email' => $donor->email,
            'WhatsApp_Number' => $user?->whatsapp_no,
            'Address_Line_01' => $user?->address_line_1,
            'Address_Line_02(optional)' => $user?->address_line_2,
            'Thana' => $user?->thana,
            'District' => $user?->district?->getTitle(),
            'Country' => $user?->country?->getTitle(),
            'Post_Code' => $user?->post_code,
            'Last_Transaction_Date' => $donor->last_transaction_date->format('Y-m-d'),
            'Status' => $user?->status,
            'Type' => $donor->donor_type->getTitle(),
            'Total_Amount_Donated' => $donor->getSuccessfulDonationAmount(),
            'Contact_Person_Name' => $user?->contact_person_name,
            'Contact_Person_Mobile' => $user?->contact_person_mobile,
            'Contact_Person_Designation' => $user?->contact_person_designation,
        ];
        ReportUpdaterService::call(ReportTypeEnum::Donor->value, $reportEntry, 'Email', true);
    }


    public function updateOrCreateTransactionReport()
    {
        $donor = $this->donor;

        if (!$donor) {
            return;
        }

        $user = $donor->user;

        $subscription = CampaignSubscription::where('campaign_id', $this->campaign_id)
            ->where('donor_id', $donor->id)
            ->where('active', true)
            ->first();

        $reportEntry = [
            'Transaction_ID' => $this->transaction_id,
            'Transaction_Date' => $this->created_at->format('Y-m-d'),
            'Donor_Name' => $donor?->name,
            'Mobile' => $donor?->phone,
            'Email' => $donor?->email,
            'Location' => $donor->user?->address_line_1,
            'Type_Of_Transaction' => $this->getDonationType(),
            'Payment_via' => $this->payment_via,
            'Payment_status' => $this->getTransactionStatus(),
            'Program' => $this->campaign?->program?->title,
            'Case' => $this->campaign?->title,
            'Subscription_Payment' => $subscription ? 'Yes': 'No',
            'Amount' => $this->amount,
        ];
        ReportUpdaterService::call(ReportTypeEnum::Transaction->value, $reportEntry);
    }
}
