<?php

namespace App\Models;

use App\Enums\ReportTypeEnum;
use App\Services\ReportUpdaterService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DownloadHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'email',
        'name',
        'mobile_no',
        'publication_id',
        'registered_user',
        'newsletter_subscribed',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($downloadHistory) {
            $downloadHistory->updateOrCreateReport();
        });
    }

    public function updateOrCreateReport()
    {
        $publication = Publication::find($this->publication_id);

        if ($publication) {
            $reportEntry1 = [
                'Books_Publication_or_Report_Name' => $publication->title,
                'Release_Date' => $publication->published_date,
                'Number_of_Downloads' => $publication->attachment->download_count,
            ];

            ReportUpdaterService::call(ReportTypeEnum::Publication->value, $reportEntry1, 'Books_Publication_or_Report_Name', true);

            $reportEntry2 = [
                'ID' => $publication->id,
                'Publication_Name' => $publication->title,
                'Type' => $publication->publication_type->getTitle(),
                'Downloader_Name' => $this->name,
                'Mobile' => (string) $this->mobile_no,
                'Email' => $this->email,
                'Registered_User' => $this->registered_user ? "Yes" : "No",
                'Download_Date' => $this->created_at->format('Y-m-d'),
                'Subscribed_to_Newsletter' => $this->newsletter_subscribed ? "Yes" : "No",
            ];

            ReportUpdaterService::call(ReportTypeEnum::Publication_Download_History->value, $reportEntry2);
        }
    }
}
