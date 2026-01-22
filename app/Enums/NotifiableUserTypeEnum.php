<?php

namespace App\Enums;

enum NotifiableUserTypeEnum: int
{
    use EnumTrait;
    case SELECTED_USERS = 1;
    case ALL_USERS = 2;
    case DONORS = 3;
    case CAMPAIGN_SUBSCRIBERS = 4;
    case NEWSLETTER_SUBSCRIBERS = 5;
}
