<?php

namespace Database\Seeders;

use App\Enums\ReportTypeEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Report;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $monthlyReport = Report::firstOrNew(['report_type' => ReportTypeEnum::Amount_Paid_By_Month]);
        $monthlyReport->save();
        $monthlyReport = Report::firstOrNew(['report_type' => ReportTypeEnum::Amount_Paid_By_Project]);
        $monthlyReport->save();
        $monthlyReport = Report::firstOrNew(['report_type' => ReportTypeEnum::Disbursement_Report]);
        $monthlyReport->save();
        $monthlyReport = Report::firstOrNew(['report_type' => ReportTypeEnum::Disbursement_Report_By_Project]);
        $monthlyReport->save();
    }
}
