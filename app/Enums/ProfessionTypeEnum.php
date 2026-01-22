<?php

namespace App\Enums;


enum ProfessionTypeEnum: int
{
    use EnumTrait;
    case Business = 1;
    case SmallBusiness = 2;
    case PrivateService = 3;
    case RetiredFromService = 4;
    case EmigrantOrExpatriate = 5;
    case Housewife = 6;
    case RetiredMilitaryService = 7;
    case Teacher = 8;
    case Doctor = 9;
    case Engineer = 10;
    case Lawyer = 11;
    case FarmerOrWageEarner = 12;
    case JobSeeker = 13;
    case Student = 14;
    case Others = 15;
}
