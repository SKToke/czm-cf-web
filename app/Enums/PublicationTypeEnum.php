<?php

namespace App\Enums;

enum PublicationTypeEnum: int
{
    use EnumTrait;
    case AUDIT_REPORT = 1;
    case BOOK = 2;
    case REPORT = 3;
    case NEWSLETTER = 4;
    case STATIC_PAGE_PUBLICATIONS = 5;
}
