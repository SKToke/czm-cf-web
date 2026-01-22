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
class PhotoGallery extends AbstractModel
{
    use SoftDeletes;
    use HasSlug;

    protected $fillable = [
        'title',
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

    public function taggedCategories(): MorphMany
    {
        return $this->morphMany(TaggedCategory::class, 'parentable');
    }

    public function categories(): HasManyThrough
    {
        return $this->hasManyThrough(Category::class, TaggedCategory::class, 'parentable_id', 'id', 'id', 'category_id')
            ->where('parentable_type', PhotoGallery::class);
    }

    public function getImageUrl(): string
    {
        return asset($this->getDynamicImageUrl($this->image));
    }

}
