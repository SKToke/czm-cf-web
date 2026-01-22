<?php

namespace App\Enums;

enum NotificationTypeEnum: int
{
    use EnumTrait;
    case GENERAL = 1;
    case CAMPAIGN_LAUNCH = 2;
    case MILESTIONES_AND_ACHIEVEMENTS = 3;
    case COUNTDOWNS = 4;
    case UPDATES_ON_PROGRESS = 5;
    case REMINDERS = 6;
    case THANK_YOU_MESSAGES = 7;
}
