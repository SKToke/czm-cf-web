<?php

namespace App\Enums;

enum ContactType: int
{
    use EnumTrait;
    case GENERAL = 1;
    case PERSONAL_ZAKAT_CONSULTANCY = 2;
    case BUSINESS_ZAKAT_CONSULTANCY = 3;
}
