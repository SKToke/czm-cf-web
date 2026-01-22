<?php

namespace App\Models;

use App\Enums\NotificationFrequencyTypeEnum;
use Illuminate\Database\Eloquent\Model;

class UserNotificationSettings extends Model
{
    protected $fillable = [
        'user_id',
        'allow_general_type',
        'allow_campaign_launch',
        'allow_campaign_milestone',
        'allow_campaign_countdown',
        'allow_campaign_progress',
        'allow_campaign_reminder',
        'allow_gratitude',
        'frequency'
    ];

    protected $casts = [
        'frequency' => NotificationFrequencyTypeEnum::class,
    ];

}
