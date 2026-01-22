<?php

namespace App\Models;

use App\Enums\ReportTypeEnum;
use App\Services\ReportUpdaterService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserZakatCalculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'mobile',
        'email',
        'zakat_type',
        'nisab_standard',
        'date',
        'total_assets',
        'total_liabilities',
        'net_zakatable_assets',
        'payable_zakat',
        'paid_to_czm',
        'registered_user',
        'calculation_form_data',
        'archived',
        'exported',
        'nisab_value',
    ];

    protected $dates = [
        'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($userZakatCalculation) {
            $userZakatCalculation->updateOrCreateReport();
        });
    }

    private function updateOrCreateReport()
    {
        $reportEntry = [
            'Id' => $this->id,
            'Name' => $this->name,
            'Mobile' => $this->mobile,
            'Email' => $this->email,
            'Type' => $this->zakat_type,
            'Nisab_Standard' => $this->nisab_standard,
            'Date' => $this->date,
            'Zakat_Amount' => $this->payable_zakat,
            'Paid_to_CZM' => $this->paid_to_czm,
            'Registered_User' => $this->registered_user ? "Yes" : "No",
        ];

        ReportUpdaterService::call(ReportTypeEnum::User_Zakat_Calculation->value, $reportEntry, 'Id', true);
    }
}
