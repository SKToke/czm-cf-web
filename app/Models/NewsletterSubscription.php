<?php

namespace App\Models;

use App\Enums\ReportTypeEnum;
use App\Services\ReportUpdaterService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsletterSubscription extends AbstractModel
{
    use SoftDeletes;
    use HasFactory;
    protected $fillable = ['name', 'email', 'phone', 'deleted_at'];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($subscription) {
            $subscription->updateOrCreateReport();
        });
    }

    private function updateOrCreateReport()
    {
        $reportEntry = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'register_date' => $this->created_at->format('Y-m-d')
        ];

        ReportUpdaterService::call(ReportTypeEnum::News_letter->value, $reportEntry);
    }
}
