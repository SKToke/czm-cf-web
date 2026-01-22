<?php

namespace App\Enums;

enum CampaignStatusEnum: int
{
    use EnumTrait;
    case UNPUBLISHED = 1;
    case PUBLISHED = 2;
    case POSTPONED = 3;
}
