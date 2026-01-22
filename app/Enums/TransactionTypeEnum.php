<?php

namespace App\Enums;

enum TransactionTypeEnum: int
{
    use EnumTrait;
    case Pending = 1;
    case Complete = 2;

    case Failed = 3;
    case Canceled = 4;
}
