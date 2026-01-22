<?php

namespace App\Enums;

enum NisabUpdateTypeEnum: int
{
    use EnumTrait;
    case GOLD = 1;
    case SILVER = 2;
    case BOTH = 3;
}
