<?php

namespace App\Enums;

enum NotificationFrequencyTypeEnum: int
{
    use EnumTrait;
    case REGULAR = 1;
    case TWO_DAYS = 2;
    case FIVE_DAYS = 3;
    case TEN_DAYS = 4;
    case ONE_MONTH = 5;
}
