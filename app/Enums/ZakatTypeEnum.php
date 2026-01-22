<?php

namespace App\Enums;

enum ZakatTypeEnum: int
{
    use EnumTrait;
    case Personal = 1;
    case Business = 2;
}
