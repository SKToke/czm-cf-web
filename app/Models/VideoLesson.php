<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
class VideoLesson extends AbstractModel
{
    use SoftDeletes;
    use HasSlug;

    protected $fillable = [
        'title',
        'youtube_link',
        'lesson_type',
        'image',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function() {
                return $this->title;
            })
            ->saveSlugsTo('slug');
    }

    public function getImageUrl(): string
    {
        return asset($this->getDynamicImageUrl($this->image));
    }
}
