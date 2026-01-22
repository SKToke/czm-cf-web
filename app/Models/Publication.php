<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\PublicationTypeEnum ;

class Publication extends AbstractModel
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'title',
        'active',
        'publication_type',
        'published_date',
        'thumbnail_image',
    ];

    protected $dates = ['deleted_at'];
    protected $casts = [
        'publication_type' => PublicationTypeEnum::class,
    ];

    public function downloadHistories(): HasMany
    {
        return $this->hasMany(DownloadHistory::class);
    }

    public function attachment(): MorphOne
    {
        return $this->morphOne(Attachment::class, 'parentable');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }


    public function scopePublications($query)
    {
        return $query->where('publication_type', PublicationTypeEnum::AUDIT_REPORT);
    }

    public function scopeBooks($query)
    {
        return $query->where('publication_type', PublicationTypeEnum::BOOK);
    }

    public function scopeReports($query)
    {
        return $query->where('publication_type', PublicationTypeEnum::REPORT);
    }

    public function scopeNewsletters($query)
    {
        return $query->where('publication_type', PublicationTypeEnum::NEWSLETTER);
    }

    public function getThumbnail(): string
    {
        return asset($this->getDynamicImageUrl($this->thumbnail_image));
    }
}
