<?php

namespace App\Http\Controllers;

use App\Enums\TransactionTypeEnum;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Report;
use App\Enums\ReportTypeEnum;
use PDF;

class ReportController extends Controller
{
    public function download(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $program = $request->input('program');

        if($startDate || $endDate || $program){
            $filteredData = $this->filterDataByDateRange($report->data, $startDate, $endDate, $program, $report->report_type);
        }
        else{
            $filteredData = $report->data;
        }

        $current_time = now()->format('Ymd_His');
        $firstName = ReportTypeEnum::from($report->report_type)->getTitle();
        $filename1 = "{$firstName}_report_{$current_time}.pdf";

        if ($request->input('downloadType')) {
            if ($report->report_type == ReportTypeEnum::Publication->value || $report->report_type == ReportTypeEnum::News_letter->value) {
                $pdf = PDF::loadView('pdf.report', ['reportData' => $filteredData, 'headers' => null, 'title' => $firstName . ' Report']);
            } else {
                $pdf = PDF::loadView('pdf.report', ['reportData' => $filteredData, 'headers' => null, 'title' => $firstName . ' Report'])->setPaper('a4', 'landscape');
            }
            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename1 . '"');
        }

        $csvData = $this->convertJsonToCsv($filteredData);
        $filename2 = "{$firstName}_report_{$current_time}.csv";

        return Response::make($csvData, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename2\"",
        ]);
    }

    public function monthlyPaymentDownload(Request $request, $id) {
        $data = [
            ['1 - 999',
                $this->numberOfPayers(1, 999, false, true),
                $this->totalAmount(1, 999, false, true),
                $this->numberOfPayers(1, 999, true, false),
                $this->totalAmount(1, 999, true, false),
                $this->cumulativePayersEndCurrentMonth(1, 999),
                $this->cumulativeAmountEndCurrentMonth(1, 999)
            ],
            ['1,000 - 4,999',
                $this->numberOfPayers(1000, 4999, false, true),
                $this->totalAmount(1000, 4999, false, true),
                $this->numberOfPayers(1000, 4999, true, false),
                $this->totalAmount(1000, 4999, true, false),
                $this->cumulativePayersEndCurrentMonth(1000, 4999),
                $this->cumulativeAmountEndCurrentMonth(1000, 4999)
            ],
            ['5,000 - 9,999',
                $this->numberOfPayers(5000, 9999, false, true),
                $this->totalAmount(5000, 9999, false, true),
                $this->numberOfPayers(5000, 9999, true, false),
                $this->totalAmount(5000, 9999, true, false),
                $this->cumulativePayersEndCurrentMonth(5000, 9999),
                $this->cumulativeAmountEndCurrentMonth(5000, 9999)
            ],
            ['10,000 and above',
                $this->numberOfPayers(10000, PHP_INT_MAX, false, true),
                $this->totalAmount(10000, PHP_INT_MAX, false, true),
                $this->numberOfPayers(10000, PHP_INT_MAX, true, false),
                $this->totalAmount(10000, PHP_INT_MAX, true, false),
                $this->cumulativePayersEndCurrentMonth(10000, PHP_INT_MAX),
                $this->cumulativeAmountEndCurrentMonth(10000, PHP_INT_MAX)
            ],
        ];

        $lastMonthTotalPayers = array_sum(array_column($data, 1));
        $lastMonthTotalAmount = array_sum(array_column($data, 2));
        $currentMonthTotalPayers = array_sum(array_column($data, 3));
        $currentMonthTotalAmount = array_sum(array_column($data, 4));
        $cumulativePayers = array_sum(array_column($data, 5));
        $cumulativeAmount = array_sum(array_column($data, 6));

        $data[] = ['Total',
            $lastMonthTotalPayers,
            $lastMonthTotalAmount,
            $currentMonthTotalPayers,
            $currentMonthTotalAmount,
            $cumulativePayers,
            $cumulativeAmount];

        $headers = [
            'Category of amount (taka)',
            'Number of zakat payers up to last month (#)',
            'Total amount up to last month (taka)',
            'Number of zakat payers (current month) (#)',
            'Total amount in current month (taka)',
            'Cumulative number of zakat payers at the end of current month (#)',
            'Cumulative amount at the end of current month (taka)',
        ];


        if ($request->input('downloadType')) {
            $pdf = PDF::loadView('pdf.report', ['reportData' => $data, 'headers' => $headers, 'title' => "Monthly Payment Report"]);
            $filename = "monthly-payment-report-" . date("Y-m-d_H-i-s") . ".pdf";

            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        }


        $fileName = "monthly-payment-report-" . date("Y-m-d_H-i-s") . ".csv";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $fileName . '";');

        $fp = fopen('php://output', 'w');

        // Add UTF-8 support by writing the BOM (byte order mark) for Excel compatibility
        fputs($fp, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

        fputcsv($fp, $headers);

        foreach ($data as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);

        exit();
    }

    public function disbursementReportDownload(Request $request) {
        $data = [
            ['1 - 999',
                $this->numberOfRecipients(1, 999, false, true, null),
                $this->totalDisbursedAmount(1, 999, false, true, null),
                $this->numberOfRecipients(1, 999, true, false, null),
                $this->totalDisbursedAmount(1, 999, true, false, null),
                $this->cumulativeRecipientsEndCurrentMonth(1, 999, null),
                $this->cumulativeDisbursedAmountEndCurrentMonth(1, 999, null)
            ],
            ['1,000 - 4,999',
                $this->numberOfRecipients(1000, 4999, false, true, null),
                $this->totalDisbursedAmount(1000, 4999, false, true, null),
                $this->numberOfRecipients(1000, 4999, true, false, null),
                $this->totalDisbursedAmount(1000, 4999, true, false, null),
                $this->cumulativeRecipientsEndCurrentMonth(1000, 4999, null),
                $this->cumulativeDisbursedAmountEndCurrentMonth(1000, 4999, null)
            ],
            ['5,000 - 9,999',
                $this->numberOfRecipients(5000, 9999, false, true, null),
                $this->totalDisbursedAmount(5000, 9999, false, true, null),
                $this->numberOfRecipients(5000, 9999, true, false, null),
                $this->totalDisbursedAmount(5000, 9999, true, false, null),
                $this->cumulativeRecipientsEndCurrentMonth(5000, 9999, null),
                $this->cumulativeDisbursedAmountEndCurrentMonth(5000, 9999, null)
            ],
            ['10,000 and above',
                $this->numberOfRecipients(10000, PHP_INT_MAX, false, true, null),
                $this->totalDisbursedAmount(10000, PHP_INT_MAX, false, true, null),
                $this->numberOfRecipients(10000, PHP_INT_MAX, true, false, null),
                $this->totalDisbursedAmount(10000, PHP_INT_MAX, true, false, null),
                $this->cumulativeRecipientsEndCurrentMonth(10000, PHP_INT_MAX, null),
                $this->cumulativeDisbursedAmountEndCurrentMonth(10000, PHP_INT_MAX, null)
            ],
        ];

        $lastMonthTotalRecipients = array_sum(array_column($data, 1));
        $lastMonthTotalDisbursedAmount = array_sum(array_column($data, 2));
        $currentMonthTotalRecipients = array_sum(array_column($data, 3));
        $currentMonthTotalDisbursedAmount = array_sum(array_column($data, 4));
        $cumulativeRecipients = array_sum(array_column($data, 5));
        $cumulativeDisbursedAmount = array_sum(array_column($data, 6));

        $data[] = [
            'Total',
            $lastMonthTotalRecipients,
            $lastMonthTotalDisbursedAmount,
            $currentMonthTotalRecipients,
            $currentMonthTotalDisbursedAmount,
            $cumulativeRecipients,
            $cumulativeDisbursedAmount
        ];

        $headers = [
            'Category of amount (taka)',
            'Number of zakat recipients up to last month (#)',
            'Total amount disbursed up to last month (taka)',
            'Number of recipients (current month) (#)',
            'Total amount in current month (taka)',
            'Cumulative number of zakat recipients at the end of current month (#)',
            'Cumulative amount at the end of current month (taka)',
        ];

        if ($request->input('downloadType')) {
            $pdf = PDF::loadView('pdf.report', ['reportData' => $data, 'headers' => $headers, 'title' => "Disbursement Report"]);
            $filename = "disbursement-report-" . date("Y-m-d_H-i-s") . ".pdf";

            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        }

        $fileName = "disbursement-report-" . date("Y-m-d_H-i-s") . ".csv";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $fileName . '";');

        $fp = fopen('php://output', 'w');

        fputs($fp, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

        fputcsv($fp, $headers);

        foreach ($data as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);

        exit();
    }

    public function disbursementReportByProgramDownload(Request $request) {
        $program = $request->input('program');
        if ($program) {
            $programTitles = Program::where('title', $program)->pluck('title');
        } else {
            $programTitles = Program::all()->pluck('title');
        }

        $data = [];

        foreach ($programTitles as $title) {
            $data[] = [
                $title,
                $this->numberOfRecipients(null, null, false, true, $title),
                $this->totalDisbursedAmount(null, null, false, true, $title),
                $this->numberOfRecipients(null, null, true, false, $title),
                $this->totalDisbursedAmount(null, null, true, false, $title),
                $this->cumulativeRecipientsEndCurrentMonth(null, null, $title),
                $this->cumulativeDisbursedAmountEndCurrentMonth(null, null, $title),
            ];
        }

        // Calculate totals for each column
        $lastMonthTotalRecipients = array_sum(array_column($data, 1));
        $lastMonthTotalDisbursedAmount = array_sum(array_column($data, 2));
        $currentMonthTotalRecipients = array_sum(array_column($data, 3));
        $currentMonthTotalDisbursedAmount = array_sum(array_column($data, 4));
        $cumulativeRecipients = array_sum(array_column($data, 5));
        $cumulativeDisbursedAmount = array_sum(array_column($data, 6));

        // Add totals row to data
        $data[] = ['Total',
            $lastMonthTotalRecipients,
            $lastMonthTotalDisbursedAmount,
            $currentMonthTotalRecipients,
            $currentMonthTotalDisbursedAmount,
            $cumulativeRecipients,
            $cumulativeDisbursedAmount];

        $headers = [
            'Project Names',
            'Number of zakat recipients up to last month (#)',
            'Total amount disbursed up to last month (taka)',
            'Number of recipients (current month) (#)',
            'Total amount in current month (taka)',
            'Cumulative number of zakat recipients at the end of current month (#)',
            'Cumulative amount at the end of current month (taka)',
        ];

        if ($request->input('downloadType')) {
            $pdf = PDF::loadView('pdf.report', ['reportData' => $data, 'headers' => $headers, 'title' => "Disbursement Report By Project"]);
            $filename = "disbursement-report-by-project-" . date("Y-m-d_H-i-s") . ".pdf";

            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        }

        $fileName = "disbursement-report-by-project-" . date("Y-m-d_H-i-s") . ".csv";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $fileName . '";');

        $fp = fopen('php://output', 'w');
        fputs($fp, $bom = chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($fp, $headers);

        foreach ($data as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);

        exit();
    }

    public function monthlyReportByProgram(Request $request) {
        $program = $request->input('program');
        if ($program) {
            $programTitles = Program::where('title', $program)->pluck('title');
        } else {
            $programTitles = Program::all()->pluck('title');
        }

        $data = [];

        foreach ($programTitles as $title) {
            $data[] = [
                $title,
                $this->numberOfPayersByProgram($title, false, true),
                $this->totalAmountByProgram($title, false, true),
                $this->numberOfPayersByProgram($title, true, false),
                $this->totalAmountByProgram($title, true, false),
                $this->cumulativePayersEndCurrentMonthByProject($title),
                $this->cumulativeAmountEndCurrentMonthByProgram($title),
            ];
        }

        // Calculate totals for each column
        $lastMonthTotalPayers = array_sum(array_column($data, 1));
        $lastMonthTotalAmount = array_sum(array_column($data, 2));
        $currentMonthTotalPayers = array_sum(array_column($data, 3));
        $currentMonthTotalAmount = array_sum(array_column($data, 4));
        $cumulativePayers = array_sum(array_column($data, 5));
        $cumulativeAmount = array_sum(array_column($data, 6));

        // Add totals row to data
        $data[] = ['Total',
            $lastMonthTotalPayers,
            $lastMonthTotalAmount,
            $currentMonthTotalPayers,
            $currentMonthTotalAmount,
            $cumulativePayers,
            $cumulativeAmount];

        $headers = [
            'Program Title',
            'Number of donors up to last month (#)',
            'Total amount up to last month',
            'Number of donors (current month) (#)',
            'Total amount in current month',
            'Cumulative number of donors at the end of current month (#)',
            'Cumulative amount at the end of current month',
        ];

        if ($request->input('downloadType')) {
            $pdf = PDF::loadView('pdf.report', ['reportData' => $data, 'headers' => $headers, 'title' => "Monthly Program Report"]);
            $filename = "monthly-program-report-" . date("Y-m-d_H-i-s") . ".pdf";

            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        }

        $fileName = "monthly-program-report-" . date("Y-m-d_H-i-s") . ".csv";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $fileName . '";');

        $fp = fopen('php://output', 'w');
        fputs($fp, $bom = chr(0xEF) . chr(0xBB) . chr(0xBF)); // Add UTF-8 BOM for Excel

        fputcsv($fp, $headers);

        foreach ($data as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);

        exit();
    }

    public function numberOfRecipients($min, $max, $currentMonth, $lastMonth, $programTitle) {
        $query = Campaign::query();

        if ($lastMonth) {
            $query = $query->where('donation_start_time', '<', Carbon::now()->startOfMonth());
        }

        if ($currentMonth) {
            $query = $query->whereBetween('donation_start_time', [Carbon::now()->startOfMonth(), Carbon::now()]);
        }

        $campaigns = $query->get();
        $validCampaigns = null;

        if ($programTitle) {
            $validCampaigns = $campaigns->filter(function ($campaign) use ($programTitle) {
                return $campaign->program->title == $programTitle;
            });
        } else {
            $validCampaigns = $campaigns->filter(function ($campaign) use ($min, $max) {
                $disbursedAmount = $campaign->getTotalDisbursedAmount();
                return $disbursedAmount <= $max && $disbursedAmount >= $min;
            });
        }

        $totalCount = $validCampaigns ? $validCampaigns->sum('number_of_recipients') : 0;

        if(!$totalCount) {
            $totalCount = 0;
        }

        return $totalCount;
    }

    public function numberOfPayers($min, $max, $currentMonth, $lastMonth) {
        $query = Donation::where('transaction_status', TransactionTypeEnum::Complete->value)
            ->whereBetween('amount', [$min, $max]);

        if ($lastMonth) {
            $query = $query->where('updated_at', '<', Carbon::now()->startOfMonth());
        }

        if ($currentMonth) {
            $query = $query->whereBetween('updated_at', [Carbon::now()->startOfMonth(), Carbon::now()]);
        }

        $nullDonorsQuery = (clone $query)->whereNull('donor_id');

        $query = $query->whereNotNull('donor_id')->distinct('donor_id');

        $countDistinctDonors = $query->count('donor_id');

        $countNullDonors = $nullDonorsQuery->count();

        $totalCount = $countDistinctDonors + $countNullDonors;

        return $totalCount;
    }

    public function totalDisbursedAmount($min, $max, $currentMonth, $lastMonth, $programTitle)
    {
        $query = Campaign::query();

        if ($lastMonth) {
            $query = $query->where('donation_start_time', '<', Carbon::now()->startOfMonth());
        }

        if ($currentMonth) {
            $query = $query->whereBetween('donation_start_time', [Carbon::now()->startOfMonth(), Carbon::now()]);
        }

        $campaigns = $query->get();
        $validCampaigns = null;

        if ($programTitle) {
            $validCampaigns = $campaigns->filter(function ($campaign) use ($programTitle) {
                return $campaign->program->title == $programTitle;
            });
        } else {
            $validCampaigns = $campaigns->filter(function ($campaign) use ($min, $max) {
                $disbursedAmount = $campaign->getTotalDisbursedAmount();
                return $disbursedAmount <= $max && $disbursedAmount >= $min;
            });
        }

        $totalDisbursedAmount = $validCampaigns ? $validCampaigns->sum(function ($campaign) {
            return $campaign->getTotalDisbursedAmount();
        }) : 0;

        if (!$totalDisbursedAmount) {
            $totalDisbursedAmount = 0;
        }
        return $totalDisbursedAmount;
    }

    public function totalAmount($min, $max,$currentMonth,$lastMonth)
    {
        $query = Donation::where('transaction_status', TransactionTypeEnum::Complete->value)
            ->whereBetween('amount', [$min, $max]);

        if ($lastMonth) {
            $query = $query->where('updated_at', '<', Carbon::now()->startOfMonth());
        } elseif ($currentMonth) {
            $query = $query->whereBetween('updated_at', [Carbon::now()->startOfMonth(), Carbon::now()]);
        }
        $total = $query->sum('amount');
        return $total;
    }

    public function cumulativeRecipientsEndCurrentMonth($min, $max, $programTitle)
    {
        $query = Campaign::where('donation_start_time', '<=', Carbon::now()->endOfMonth());

        $campaigns = $query->get();
        $validCampaigns = null;

        if ($programTitle) {
            $validCampaigns = $campaigns->filter(function ($campaign) use ($programTitle) {
                return $campaign->program->title == $programTitle;
            });
        } else {
            $validCampaigns = $campaigns->filter(function ($campaign) use ($min, $max) {
                $disbursedAmount = $campaign->getTotalDisbursedAmount();
                return $disbursedAmount <= $max && $disbursedAmount >= $min;
            });
        }

        $totalCount = $validCampaigns ? $validCampaigns->sum('number_of_recipients') : 0;

        if(!$totalCount) {
            $totalCount = 0;
        }

        return $totalCount;
    }

    public function cumulativePayersEndCurrentMonth($min, $max)
    {
        $query = Donation::where('transaction_status', TransactionTypeEnum::Complete->value)
            ->where('updated_at', '<=', Carbon::now()->endOfMonth())
            ->whereBetween('amount', [$min, $max]);

        $nullDonorsQuery = (clone $query)->whereNull('donor_id');

        $query = $query->whereNotNull('donor_id')->distinct('donor_id');

        $countDistinctDonors = $query->count('donor_id');

        $countNullDonors = $nullDonorsQuery->count();

        $totalCumulativeCount = $countDistinctDonors + $countNullDonors;

        return $totalCumulativeCount;
    }

    public function cumulativeAmountEndCurrentMonth($min, $max){
        $cumulativeAmount = Donation::where('transaction_status', TransactionTypeEnum::Complete->value)
            ->where('updated_at', '<=', Carbon::now()->endOfMonth())
            ->whereBetween('amount', [$min, $max])
            ->sum('amount');

        return $cumulativeAmount;
    }

    public function cumulativeDisbursedAmountEndCurrentMonth($min, $max, $programTitle){
        $query = Campaign::where('donation_start_time', '<=', Carbon::now()->endOfMonth());

        $campaigns = $query->get();
        $validCampaigns = null;

        if ($programTitle) {
            $validCampaigns = $campaigns->filter(function ($campaign) use ($programTitle) {
                return $campaign->program->title == $programTitle;
            });
        } else {
            $validCampaigns = $campaigns->filter(function ($campaign) use ($min, $max) {
                $disbursedAmount = $campaign->getTotalDisbursedAmount();
                return $disbursedAmount <= $max && $disbursedAmount >= $min;
            });
        }

        $totalDisbursedAmount = $validCampaigns ? $validCampaigns->sum(function ($campaign) {
            return $campaign->getTotalDisbursedAmount();
        }) : 0;

        if (!$totalDisbursedAmount) {
            $totalDisbursedAmount = 0;
        }

        return $totalDisbursedAmount;
    }

    public function numberOfPayersByProgram($programTitle, $currentMonth, $lastMonth) {
        $query = Donation::where('transaction_status', TransactionTypeEnum::Complete->value);

        $query = $query->whereHas('campaign.program', function ($query) use ($programTitle) {
            $query->where('title', $programTitle);
        });

        if ($lastMonth) {
            $query = $query->where('updated_at', '<', Carbon::now()->startOfMonth());
        }

        if ($currentMonth) {
            $query = $query->whereBetween('updated_at', [Carbon::now()->startOfMonth(), Carbon::now()]);
        }

        $nullDonorsQuery = (clone $query)->whereNull('donor_id');

        $query = $query->whereNotNull('donor_id')->distinct('donor_id');

        $countDistinctDonors = $query->count('donor_id');

        $countNullDonors = $nullDonorsQuery->count();

        $totalCount = $countDistinctDonors + $countNullDonors;

        return $totalCount;
    }

    public function totalAmountByProgram($programTitle,$currentMonth,$lastMonth)
    {
        $query = Donation::where('transaction_status', TransactionTypeEnum::Complete->value);

        $query = $query->whereHas('campaign.program', function ($query) use ($programTitle) {
            $query->where('title', $programTitle);
        });

        if ($lastMonth) {
            $query = $query->where('updated_at', '<=', Carbon::now()->startOfMonth());
        } elseif ($currentMonth) {
            $query = $query->whereBetween('updated_at', [Carbon::now()->startOfMonth(), Carbon::now()]);
        }
        $total = $query->sum('amount');
        return $total;
    }


    public function cumulativePayersEndCurrentMonthByProject($programTitle)
    {
        $query = Donation::where('transaction_status', TransactionTypeEnum::Complete->value)
            ->where('updated_at', '<=', Carbon::now()->endOfMonth())
            ->whereHas('campaign.program', function ($query) use ($programTitle) {
                $query->where('title', $programTitle);
            });

        $nullDonorsQuery = (clone $query)->whereNull('donor_id');

        $query = $query->whereNotNull('donor_id')->distinct('donor_id');

        $countDistinctDonors = $query->count('donor_id');

        $countNullDonors = $nullDonorsQuery->count();

        $totalCumulativeCount = $countDistinctDonors + $countNullDonors;

        return $totalCumulativeCount;
    }

    public function cumulativeAmountEndCurrentMonthByProgram($programTitle) {
        $cumulativeAmount = Donation::whereHas('campaign.program', function ($query) use ($programTitle) {
            $query->where('title', $programTitle);
        })
            ->where('transaction_status', TransactionTypeEnum::Complete->value)
            ->where('updated_at', '<=', Carbon::now()->endOfMonth())
            ->sum('amount');

        return $cumulativeAmount;
    }


    private function convertJsonToCsv($jsonData)
    {
        if (empty($jsonData)) {
            return null;
        }

        $attributes = array_keys($jsonData[0]);
        $csv = fopen('php://temp', 'r+');

        fputcsv($csv, $attributes);

        foreach ($jsonData as $row) {
            fputcsv($csv, $row);
        }

        rewind($csv);

        $output = stream_get_contents($csv);

        fclose($csv);

        return $output;
    }

    private function filterDataByDateRange($data, $startDate, $endDate, $program, $reportType)
    {
        $filteredData = [];
        $dateField = $this->getDateFieldForReportType($reportType);

        if ($startDate || $endDate) {
            foreach ($data as $entry) {
                $registerDate = $entry[$dateField];
                if ($registerDate >= $startDate && $registerDate <= $endDate) {
                    $filteredData[] = $entry;
                }
            }
        } else {
            $filteredData = $data;
        }

        if ($program) {
            $filteredData2 = [];
            foreach ($filteredData as $entry) {
                if ($entry['Program'] == $program) {
                    $filteredData2[] = $entry;
                }
            }
            $filteredData = $filteredData2;
        }

        return $filteredData;
    }

    private function getDateFieldForReportType($reportType)
    {
        $reportTypeDateFieldMapping = [
            ReportTypeEnum::News_letter->value => 'register_date',
            ReportTypeEnum::Donor->value => 'Last_Transaction_Date',
            ReportTypeEnum::Campaign->value => 'Date_of_Created',
            ReportTypeEnum::Transaction->value => 'Transaction_Date',
            ReportTypeEnum::User_Zakat_Calculation->value => 'Date',
            ReportTypeEnum::Publication->value => 'Release_Date',
            ReportTypeEnum::Publication_Download_History->value => 'Download_Date',
        ];

        return $reportTypeDateFieldMapping[$reportType] ?? null;
    }
}
