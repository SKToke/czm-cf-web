<?php

namespace App\Services;

use App\Models\Report;

class ReportUpdaterService
{
    /**
     * Static method to initialize and call the report updater.
     *
     * @param string $reportType
     * @param array $reportEntry
     * @param string|null $uniqueAttribute
     * @param bool $checkExisting
     */
    public static function call($reportType, $reportEntry, $uniqueAttribute = null, $checkExisting = false)
    {
        $updater = new static($reportType, $reportEntry, $uniqueAttribute, $checkExisting);
        $updater->updateOrCreateReport();
    }

    protected $reportType;
    protected $reportEntry;
    protected $uniqueAttribute;
    protected $checkExisting;

    public function __construct($reportType, $reportEntry, $uniqueAttribute, $checkExisting)
    {
        $this->reportType = $reportType;
        $this->reportEntry = $reportEntry;
        $this->uniqueAttribute = $uniqueAttribute;
        $this->checkExisting = $checkExisting;
    }

    protected function updateOrCreateReport()
    {
        $report = Report::firstOrNew(['report_type' => $this->reportType]);
        $reportData = $report->data ?: [];

        if ($this->checkExisting && $this->uniqueAttribute) {
            $this->updateOrAddEntry($reportData);
        } else {
            $reportData[] = $this->reportEntry;
        }

        $report->data = $reportData;

        $report->save();
    }

    protected function updateOrAddEntry(&$reportData)
    {
        $existingEntryIndex = null;

        foreach ($reportData as $index => $entry) {
            if (isset($entry[$this->uniqueAttribute]) && $entry[$this->uniqueAttribute] == $this->reportEntry[$this->uniqueAttribute]) {
                $existingEntryIndex = $index;
                break;
            }
        }

        if (is_null($existingEntryIndex)) {
            $reportData[] = $this->reportEntry;
        } else {
            $reportData[$existingEntryIndex] = $this->reportEntry;
        }
    }
}
