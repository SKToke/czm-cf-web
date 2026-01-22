<?php

namespace App\Enums;

enum CampaignSubscriptionTypeEnum: int
{
    use EnumTrait;
    case MONTHLY = 1;
    case QUARTERLY = 2;
    case HALF_YEARLY = 3;
    case YEARLY = 4;
}
