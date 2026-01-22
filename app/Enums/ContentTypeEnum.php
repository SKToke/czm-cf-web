<?php

namespace App\Enums;

enum ContentTypeEnum: int
{
    use EnumTrait;
    case BLOG = 1;
    case NEWS = 2;
    case STORY = 3;
    case QURANIC_VERSE = 4;
    case SADAQAH = 5;
    case CASH_WAQF = 6;
    case QARD_AL_HASAN = 7;
}

