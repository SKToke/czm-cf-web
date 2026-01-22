<?php

namespace App\Models;

use App\Enums\CampaignStatusEnum;
use App\Enums\CampaignTypeEnum;
use App\Enums\ReportTypeEnum;
use App\Enums\TransactionTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Models\Donation;
use Illuminate\Support\Facades\DB;
use App\Services\ReportUpdaterService;


class Campaign extends AbstractModel
{
    use SoftDeletes;
    use HasSlug;

    protected $fillable = [
        'title',
        'campaign_id',
        'description',
        'campaign_type',
        'campaign_status',
        'program_id',
        'urgency_status',
        'donation_start_time',
        'donation_end_time',
        'allocated_amount',
        'share_count',
        'image_paths',
        'number_of_recipients',
        'deleted_at',
    ];

    protected $casts = [
        'campaign_type' => CampaignTypeEnum::class,
        'campaign_status' => CampaignStatusEnum::class,
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function setImagePathsAttribute($imagePaths)
    {
        if (is_array($imagePaths)) {
            $this->attributes['image_paths'] = json_encode($imagePaths);
        }
    }

    public function getImagePathsAttribute($imagePaths)
    {
        return json_decode($imagePaths, true);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function successfulDonations(): HasMany
    {
        return $this->hasMany(Donation::class)->where('transaction_status', TransactionTypeEnum::Complete->value);
    }

    public function taggedCategories(): MorphMany
    {
        return $this->morphMany(TaggedCategory::class, 'parentable');
    }

    public function categories(): HasManyThrough
    {
        return $this->hasManyThrough(Category::class, TaggedCategory::class, 'parentable_id', 'id', 'id', 'category_id')
                    ->where('parentable_type', Campaign::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'parentable');
    }

    public function hasAttachments()
    {
        return $this->attachments()->count() > 0;
    }

    public function getAttachments(): array
    {
        $allAttachments = $this->attachments;

        $attachmentPaths = [];

        foreach ($allAttachments as $attachment) {
            $filePath = '/admin/' . $attachment->file;
            $fileExists = Storage::disk('public')->exists($filePath);
            if ($fileExists) {
                $attachmentPaths [] = [
                    'title' => $attachment->title,
                    'url' => Storage::disk('public')->url($filePath),
                ];
            }
        }

        return $attachmentPaths;
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class,'campaign_id');
    }

    public function campaignUpdates()
    {
        return $this->hasMany(CampaignUpdate::class,'campaign_id');
    }

    public function getCampaignUpdates()
    {
        return $this->campaignUpdates()->orderBy('created_at', 'desc')->get();
    }

    public function getTotalDisbursedAmount()
    {
        return $this->campaignUpdates()->sum('disbursed_amount');
    }

    public function campaignSubscriptions()
    {
        return $this->hasMany(CampaignSubscription::class,'campaign_id');
    }

    public function getCampaignSubscriptions()
    {
        return $this->campaignUpdates()->get();
    }

    public function getThumbnailImage(): string
    {
        return asset($this->getDynamicImageUrl($this->thumbnail_image));
    }

    public function hasDonationValidity()
    {
        return $this->donation_end_time >= now();
    }

    public function hasImages(): bool
    {
        $imagesArray = $this->image_paths;
        if (!is_array($this->image_paths)) {
            $imagesArray = json_decode($this->image_paths, true);
        }
        if ($imagesArray !== null) {
            return count($imagesArray) > 0;
        } else {
            return false;
        }
    }

    public function getImages(): array
    {
        $allImages = $this->image_paths;
        if (!is_array($allImages)) {
            $allImages = json_decode($allImages, true);
        }
        $allImageUrl = [];
        foreach ($allImages as $image){
            $allImageUrl[] = asset($this->getDynamicImageUrl($image));
        }
        return $allImageUrl;
    }

    public function getFormattedTitle()
    {
        return Str::limit($this->title, 65, '...');
    }

    public function getFormattedProgramTitle()
    {
        return Str::limit($this->program->title, 35, '...');
    }

    public function getFundCount()
    {
        return $this->successfulDonations()->sum('amount');
    }

    public function getFundPercentage()
    {
        return $this->allocated_amount > 0 ? min(($this->getFundCount() / $this->allocated_amount) * 100, 100) : 0;
    }

    public function remainingTime()
    {
        return Carbon::parse($this->donation_end_time)->isFuture() ? Carbon::parse($this->donation_end_time)->diff(now()) : now()->diff(now());
    }

    public function getRemainingDays()
    {
        return $this->remainingTime()->days;
    }

    public function getTotalSupporters()
    {
        $donations = $this->successfulDonations()->with('donor')->get();
        if ($donations->isEmpty()) {
            return 0;
        }

        $uniqueDonors = $donations->pluck('donor')->unique('email');
        if (is_null($uniqueDonors)) {
            return 0;
        }

        return $uniqueDonors->count();
    }

    public function getLastDonationDate()
    {
        return $this->successfulDonations()->max('created_at');
    }

    public function getFormatttedLastDonationDate(){
        return Carbon::parse($this->getLastDonationDate())->format('d F, Y');
    }

    public function getDonations(): array
    {
        return $this->successfulDonations()->get()->toArray();
    }

    public function getFormattedCampaignUpdates()
    {
        $campaignUpdates = $this->getCampaignUpdates();
        $allUpdates = '<ul>';
        foreach ($campaignUpdates as $update) {
            $updatedAt = Carbon::parse($update->updated_at)->format('Y-m-d h:i:s A');
            $allUpdates .= '<li>' . $update->title . ':<p>'. $update->getCustomMessage() . '</p>';
            $allUpdates .= '<p>' . $update->getFormattedAttachments() . '</p>';
            $allUpdates .= '<p><b>Last Modified at: '. $updatedAt . '</b></p>';
            $allUpdates .= '</li>';
        }
        $allUpdates .= '</ul>';
        return $allUpdates;
    }

    public function isPublished(): bool
    {
        return $this->campaign_status === CampaignStatusEnum::PUBLISHED;
    }

    public function isAvailable(): bool
    {
        return ($this->campaign_status == CampaignStatusEnum::PUBLISHED) ||
            ($this->campaign_status == CampaignStatusEnum::POSTPONED);
    }

    public function isSubscribable(): bool
    {
        return $this->hasDonationValidity() &&
            $this->campaign_type === CampaignTypeEnum::SUBSCRIPTION &&
            $this->campaign_status == CampaignStatusEnum::PUBLISHED;
    }

    public function relatedCampaigns(int $limit = null) {
        $categoryIds =  $this->categories->pluck('id')->toArray();

        $relatedCampaigns = Campaign::where('campaign_status', CampaignStatusEnum::PUBLISHED);

        $relatedCampaigns->where(function ($query) use ($categoryIds) {
            $query->whereHas('categories', function ($subquery) use ($categoryIds) {
                $subquery->whereIn('categories.id', $categoryIds);
            })
            ->orWhere('program_id', $this->program_id);
        });

        $relatedCampaigns = $relatedCampaigns->where('campaigns.id', '!=', $this->id)->distinct()->get();

        if (!$limit) {
            $relatedCampaigns = $relatedCampaigns->sortBy('donation_end_time')->take($limit);
        } else {
            $relatedCampaigns = $relatedCampaigns->sortBy('donation_end_time');
        }

        return $relatedCampaigns && $relatedCampaigns->isNotEmpty() ? $relatedCampaigns : null;
    }

    public function userRelatedCampaigns(int $limit = null, User $user = null) {
        if (!$user) {
            $user = auth()->user();
        }

        if ($user && $user->active && $user->donor && $user->donor->donations() ) {
            $donations = $user->donor->donations;

            $categoryIds = $donations->flatMap(function ($donation) {
                if ($donation->campaign) {
                    return $donation->campaign->categories->pluck('id');
                }
            })->unique()->toArray();

            $programIds = $donations->flatMap(function ($donation) {
                $campaign = $donation->campaign()->first();
                if ($campaign) {
                    return $campaign->program()->pluck('id');
                }
            })->unique()->toArray();

            $relatedCampaigns = Campaign::where('campaign_status', CampaignStatusEnum::PUBLISHED);

            $relatedCampaigns->where(function ($query) use ($categoryIds, $programIds) {
                $query->whereHas('categories', function ($subquery) use ($categoryIds) {
                    $subquery->whereIn('categories.id', $categoryIds);
                })
                ->orWhereIn('program_id', $programIds);
            });

            $relatedCampaigns = $relatedCampaigns->where('campaigns.id', '!=', $this->id)->distinct()->get();

            if (!$limit) {
                $relatedCampaigns = $relatedCampaigns->sortBy('donation_end_time')->take($limit);
            } else {
                $relatedCampaigns = $relatedCampaigns->sortBy('donation_end_time');
            }
            return $relatedCampaigns;
        } else {
            return null;
        }
    }

    public function topDonorsInfo()
    {
        return Donation::join('donors', 'donations.donor_id', '=', 'donors.id')
            ->where('campaign_id', $this->id)
            ->where('transaction_status', TransactionTypeEnum::Complete->value)
            ->groupBy('donors.id')
            ->selectRaw('donors.name, SUM(donations.amount) as total_amount')
            ->orderByDesc(DB::raw('SUM(donations.amount)'))
            ->limit(5)
            ->get();
    }

    public static function findByCustomId($campaignId){
        return Campaign::where('campaign_id', $campaignId)->first();
    }

    public static function isDonatable($campaignId): bool
    {
        $campaign = self::findByCustomId($campaignId);
        return $campaign && $campaign->hasDonationValidity() && $campaign->isPublished();
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($campaign) {
            if ($campaign->campaign_id != 1000) {
                $campaign->updateOrCreateReport();
            }
        });
    }

    public function updateOrCreateReport()
    {
        $reportEntry = [
            'ID' => $this->id,
            'Case_ID' => $this->campaign_id,
            'Case_Title' => $this->title,
            'Program' => $this->program->title,
            'Date_of_Created' => $this->created_at->format('Y-m-d'),
            'Amount_Asked' => $this->allocated_amount,
            'Amount_Received' => $this->getFundCount(),
            'Amount_Disbursed' => $this->getTotalDisbursedAmount(),
            'Number_of_Contributor' => $this->getTotalSupporters(),
            'Number_of_Share' => $this->share_count,
        ];

        ReportUpdaterService::call(ReportTypeEnum::Campaign->value, $reportEntry, 'Case_ID', true);
    }
}
