<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\ContactType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactUsQuery extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'email', 'mobile_no', 'message', 'contact_type', 'campaign_id'];

    protected $casts = [
        'contact_type' => ContactType::class,
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function scopeGeneral($query)
    {
        return $query->where('contact_type', ContactType::GENERAL->value);
    }

    public function scopeZakatConsultancy($query)
    {
        return $query->where('contact_type', '!=', ContactType::GENERAL->value);
    }

    public function getQueryTypeAttribute()
    {
        return match ($this->contact_type) {
            ContactType::PERSONAL_ZAKAT_CONSULTANCY->value => 'Personal',
            ContactType::BUSINESS_ZAKAT_CONSULTANCY->value => 'Business',
            default => 'General',
        };
    }
}
