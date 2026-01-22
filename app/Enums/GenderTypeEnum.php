<?php

namespace App\Enums;

enum GenderTypeEnum: int
{
    use EnumTrait;
    case Male = 1;
    case Female = 2;
}
