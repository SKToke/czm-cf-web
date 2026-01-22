<?php

namespace App\Enums;

enum CampaignTypeEnum: int
{
    use EnumTrait;
    case ONETIME = 1;
    case SUBSCRIPTION = 2;
}
