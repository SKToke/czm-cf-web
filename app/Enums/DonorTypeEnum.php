<?php

namespace App\Enums;

enum DonorTypeEnum: int
{
    use EnumTrait;
    case INDIVIDUAL = 1;
    case BUSINESS = 2;
    case UNREGISTERED = 3;
    case UNKNOWN = 4;
}
