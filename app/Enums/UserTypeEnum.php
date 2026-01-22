<?php

namespace App\Enums;

enum UserTypeEnum: int
{
    use EnumTrait;
    case Individual = 1;
    case Business = 2;
}
