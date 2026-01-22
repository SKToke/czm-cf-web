<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Program extends AbstractModel
{
    use SoftDeletes;
    use HasSlug;

    protected $fillable = [
        'title',
        'objective',
        'activities_description',
        'strategy',
        'deleted_at',
        'default',
        'subtitle',
        'slogan',
        'counter_1_label',
        'counter_1_value',
        'counter_2_label',
        'counter_2_value',
        'counter_3_label',
        'counter_3_value',
        'counter_4_label',
        'counter_4_value',
        'photos',
        'links',
        'active'
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function() {
                return $this->title . ($this->subtitle ? ' - ' . $this->subtitle : '');
            })
            ->saveSlugsTo('slug');
    }

    public function links(): HasMany
    {
        return $this->hasMany(ProgramLink::class);
    }

    public function category(): HasOne
    {
        return $this->hasOne(Category::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function setPhotosAttribute($photos)
    {
        if (is_array($photos)) {
            $this->attributes['photos'] = json_encode($photos);
        }
    }

    public function getPhotosAttribute($photos)
    {
        return json_decode($photos, true);
    }

    public function getLogo(): string
    {
        return asset($this->getDynamicImageUrl($this->program_logo));
    }

    public function title_with_slogan(): string
    {
        if($this->slogan) return $this->title . ' - ' . $this->slogan;
        return $this->title;
    }

    public function title_with_subtitle(): string
    {
        if($this->subtitle) return $this->title . ' - ' . $this->subtitle;
        return $this->title;
    }


    public static function getDefaults(): mixed
    {
        return Program::where('default', true)->where('active', true)->get();
    }

    public function hasCounter($counterNumber): bool
    {
        return $this->{'counter_' . $counterNumber . '_label'}
            && $this->{'counter_' . $counterNumber . '_value'};
    }

    public function hasAnyCounter(): bool
    {
        return $this->hasCounter(1)
            || $this->hasCounter(2)
            || $this->hasCounter(3)
            || $this->hasCounter(4);
    }

    public function getAllCounters(): array
    {
        $counters = [];

        if ($this->hasCounter(1)) {
            $counters[] = [
                'label' => $this->counter_1_label,
                'value' => (int)$this->counter_1_value,
                'icon' => 'hand-holding-heart',
            ];
        }

        if ($this->hasCounter(2)) {
            $counters[] = [
                'label' => $this->counter_2_label,
                'value' => (int)$this->counter_2_value,
                'icon' => 'globe',
            ];
        }

        if ($this->hasCounter(3)) {
            $counters[] = [
                'label' => $this->counter_3_label,
                'value' => (int)$this->counter_3_value,
                'icon' => 'dove',
            ];
        }

        if ($this->hasCounter(4)) {
            $counters[] = [
                'label' => $this->counter_4_label,
                'value' => (int)$this->counter_4_value,
                'icon' => 'handshake',
            ];
        }

        return $counters;
    }

    public function getPhotos(): array
    {
        $allPhotos = $this->photos;
        if (!is_array($allPhotos)) {
            $allPhotos = json_decode($allPhotos, true);
        }
        $allPhotoUrl = [];
        foreach ($allPhotos as $photo){
            $allPhotoUrl[] = asset($this->getDynamicImageUrl($photo));
        }
        return $allPhotoUrl;
    }

    public function hasPhotos(): bool
    {
        $imagesArray = $this->photos;
        if (!is_array($this->photos)) {
            $imagesArray = json_decode($this->photos, true);
        }
        if ($imagesArray !== null) {
            return count($imagesArray) > 0;
        } else {
            return false;
        }
    }
}
