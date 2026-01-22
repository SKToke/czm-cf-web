<?php

namespace App\Enums;

enum ReportTypeEnum: int
{
    use EnumTrait;
    case News_letter = 1;
    case Donor = 2;
    case Campaign = 3;
    case Transaction = 4;
    case Amount_Paid_By_Month = 5;
    case Publication = 6;
    case Publication_Download_History = 7;
    case User_Zakat_Calculation = 8;
    case Amount_Paid_By_Project = 9;
    case Disbursement_Report = 10;
    case Disbursement_Report_By_Project = 11;
}
