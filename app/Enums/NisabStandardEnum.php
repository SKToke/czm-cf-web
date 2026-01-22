<?php

namespace App\Enums;

enum NisabStandardEnum: int
{
    use EnumTrait;
    case Gold = 1;
    case Silver = 2;
}
