<?php

namespace App\Models;

use App\Enums\CountryTypeEnum;
use App\Enums\DistrictTypeEnum;
use App\Enums\DonationTypeEnum;
use App\Enums\DonorTypeEnum;
use App\Enums\GenderTypeEnum;
use App\Enums\NotificationFrequencyTypeEnum;
use App\Enums\NotificationTypeEnum;
use App\Enums\ProfessionTypeEnum;
use App\Enums\TransactionTypeEnum;
use App\Enums\UserTypeEnum;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'address_line_1', 'address_line_2',
        'post_code', 'date_of_birth', 'mobile_no', 'whatsapp_no',
        'profession', 'thana', 'district', 'country', 'gender',
        'user_type', 'contact_person_name', 'contact_person_mobile',
        'contact_person_designation', 'active', 'admin', 'social_info', 'email_verified_at', 'device_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'user_type' => UserTypeEnum::class,
        'profession' => ProfessionTypeEnum::class,
        'gender' => GenderTypeEnum::class,
        'country' => CountryTypeEnum::class,
        'district' => DistrictTypeEnum::class,
    ];

    public function notifications()
    {
        return $this->belongsToMany(Notification::class, 'user_notifications', 'user_id', 'notification_id')->withTimestamps();
    }

    public function validNotifications()
    {
        if (!is_null($this->notifications)) {
            $notifications = $this->notifications();
            $settings = $this->notificationSettings()?->first();
            if ($settings) {
                if ($settings->allow_general_type == false) {
                    $notifications->wherenot('type', NotificationTypeEnum::GENERAL->value);
                }
                if ($settings->allow_campaign_launch == false) {
                    $notifications->wherenot('type', NotificationTypeEnum::CAMPAIGN_LAUNCH->value);
                }
                if ($settings->allow_campaign_milestone == false) {
                    $notifications->wherenot('type', NotificationTypeEnum::MILESTIONES_AND_ACHIEVEMENTS->value);
                }
                if ($settings->allow_campaign_countdown == false) {
                    $notifications->wherenot('type', NotificationTypeEnum::COUNTDOWNS->value);
                }
                if ($settings->allow_campaign_progress == false) {
                    $notifications->wherenot('type', NotificationTypeEnum::UPDATES_ON_PROGRESS->value);
                }
                if ($settings->allow_campaign_reminder == false) {
                    $notifications->wherenot('type', NotificationTypeEnum::REMINDERS->value);
                }
                if ($settings->allow_gratitude == false) {
                    $notifications->wherenot('type', NotificationTypeEnum::THANK_YOU_MESSAGES->value);
                }
            }
            return $notifications;
        } else {
            return null;
        }
    }

    public function getUnreadNotifications()
    {
        if (!is_null($this->notifications)) {
            $notifications = $this->validNotifications()->where('read_at', null);
            $frequencySettings = $this->notificationSettings()?->first()?->frequency;
            if ($frequencySettings) {
                if ($frequencySettings->value == NotificationFrequencyTypeEnum::TWO_DAYS->value) {
                    $notifications->whereRaw('DATEDIFF(NOW(), notifications.created_at) > 0')
                        ->whereRaw('DATEDIFF(NOW(), notifications.created_at) % 2 = 1');
                } elseif ($frequencySettings->value == NotificationFrequencyTypeEnum::FIVE_DAYS->value) {
                    $notifications->whereRaw('DATEDIFF(NOW(), notifications.created_at) > 0')
                        ->whereRaw('DATEDIFF(NOW(), notifications.created_at) % 5 = 0');
                } elseif ($frequencySettings->value == NotificationFrequencyTypeEnum::TEN_DAYS->value) {
                    $notifications->whereRaw('DATEDIFF(NOW(), notifications.created_at) > 0')
                        ->whereRaw('DATEDIFF(NOW(), notifications.created_at) % 10 = 0');
                } elseif ($frequencySettings->value == NotificationFrequencyTypeEnum::ONE_MONTH->value) {
                    $notifications->whereRaw('DATEDIFF(NOW(), notifications.created_at) > 0')
                        ->whereRaw('DATEDIFF(NOW(), notifications.created_at) % 30 = 0');
                }
            }

            return $notifications->orderByDesc('created_at')->get();
        }
        return null;
    }

    public function getArchivedNotifications()
    {
        if (!is_null($this->notifications)) {
            $notifications = $this->validNotifications()->wherenot('read_at', null);
            return $notifications->orderByDesc('created_at')->get();
        }
        return null;
    }

    public function notificationSettings()
    {
        return $this->hasOne(UserNotificationSettings::class);
    }


    public function zakatCalculations(): HasMany
    {
        return $this->hasMany(UserZakatCalculation::class,'email', 'email');
    }

    public function successfulDonations()
    {
        $donor = Donor::where('email', $this->email)->first();
        if ($donor && !is_null($donor->donations) && $donor->donations->count() > 0) {
            return $donor->successfulDonations();
        }
        return null;
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getStatusAttribute()
    {
        if ($this->active === null) {
            return 'Discontinue';
        }

        return $this->active ? 'Active' : 'Inactive';
    }

    public function roles(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_users_table');

        $relatedModel = config('admin.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'role_id');
    }

    public function hasValidRoles(): bool
    {
        if (count($this->roles) > 1) {
            return true;
        }
        if (count($this->roles) == 1) {
            return !$this->roles->pluck('slug')->contains('donor');
        }
        return false;
    }

    public function isRole(string $role): bool
    {
        return $this->roles->pluck('slug')->contains($role);
    }

    /**
     * A User has and belongs to many permissions.
     *
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        $pivotTable = config('admin.database.user_permissions_table');

        $relatedModel = config('admin.database.permissions_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'permission_id');
    }

    public function donor(): HasOne
    {
        return $this->hasOne(Donor::class);
    }

    public function findOrCreateDonor(){
        $donor = Donor::firstOrNew(['email' => $this->email]);
        $donor->name = $this->getFullNameAttribute();
        if ($donor->email == "super_admin@czm.com") {
            $donor->phone = '***********';
        } else {
            $donor->phone = $this->mobile_no;
        }
        $donor->user_id = $this->id;
        if($this->user_type === UserTypeEnum::Individual){
            $donor->donor_type = DonorTypeEnum::INDIVIDUAL;
        }else if($this->user_type === UserTypeEnum::Business){
            $donor->donor_type = DonorTypeEnum::BUSINESS;
        } else {
            $donor->donor_type = DonorTypeEnum::UNKNOWN;
        }
        $donor->save();

        return $donor;
    }

    public function hasCampaignSubscription($campaignId)
    {
        if ($this->donor) {
            return $this->donor->campaignSubscriptions()
                ->where('campaign_id', $campaignId)
                ->where('active', true)
                ->exists();
        }
        return false;
    }

    public function allCampaignSubscriptions()
    {
        if ($this->active) {
            return $this->donor ? $this->donor->campaignSubscriptions()->where('active', true) : null;
        }
        return null;
    }

    public function getUpcomingDonations()
    {
        $upcomingDonations = null;

        if ($this->active && !is_null($this->allCampaignSubscriptions()) && $this->allCampaignSubscriptions()->get()->count() > 0) {
            $upcomingDonations = $this->allCampaignSubscriptions()
                ->where('next_donation_date', '>=', now())
                ->where('due_amount', '<', 0)
                ->orderBy('next_donation_date', 'asc')
                ->get();
        }
        return $upcomingDonations;
    }

    public static function boot()
    {
        parent::boot();

        static::saved(function ($user) {
            /**
             * After save, check if the user's email has been a donor.
             * If email existed as a donor, update the donor record.
             */
            if(Donor::where('email', $user->email)->exists())
                $user->findOrCreateDonor();
        });
    }

    public function isDonor(){
        return $this->donor !== null;
    }

    public function getTotalZakatAmount(): string
    {
        if(!$this->isDonor()) return 0;

        $currentDonor = $this->donor;
        return $currentDonor->donations->where('donation_type', DonationTypeEnum::ZAKAT)
            ->where('transaction_status', TransactionTypeEnum::Complete->value)->sum('amount');
    }

    public function getTotalSadakahAmount(): string
    {
        if(!$this->isDonor()) return 0;

        $currentDonor = $this->donor;
        return $currentDonor->donations->where('donation_type', DonationTypeEnum::SADAKAH_OR_DONATION)
            ->where('transaction_status', TransactionTypeEnum::Complete->value)->sum('amount');
    }

    public function getTotalWaqfAmount(): string
    {
        if(!$this->isDonor()) return 0;

        $currentDonor = $this->donor;
        return $currentDonor->donations->where('donation_type', DonationTypeEnum::Cash_Waqf)
            ->where('transaction_status', TransactionTypeEnum::Complete->value)->sum('amount');
    }

    public function getTotalDonationAmount(): int
    {
        return (int)$this->getTotalWaqfAmount() + (int)$this->getTotalZakatAmount() + (int)$this->getTotalSadakahAmount();
    }
}
