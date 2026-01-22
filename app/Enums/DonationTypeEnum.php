<?php

namespace App\Enums;

enum DonationTypeEnum: int
{
    use EnumTrait;
    case ZAKAT = 1;
    case SADAKAH_OR_DONATION = 2;
    case Cash_Waqf = 3;
}
