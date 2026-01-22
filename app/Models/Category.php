<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use SoftDeletes;
    use HasSlug;

    protected $fillable = [
        'title',
        'program_id',
        'parent_id',
        'deleted_at',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function taggedCategories(): HasMany
    {
        return $this->hasMany(TaggedCategory::class);
    }

    public function campaigns()
    {
        return $this->hasManyThrough(Campaign::class, TaggedCategory::class, 'category_id', 'id', 'id', 'parentable_id')
            ->where('parentable_type', Campaign::class);
    }

    public function contents()
    {
        return $this->hasManyThrough(Content::class, TaggedCategory::class, 'category_id', 'id', 'id', 'parentable_id')
            ->where('parentable_type', Content::class);
    }

    public function videogalleries()
    {
        return $this->hasManyThrough(VideoGallery::class, TaggedCategory::class, 'category_id', 'id', 'id', 'parentable_id')
            ->where('parentable_type', VideoGallery::class);
    }

    public function photogalleries()
    {
        return $this->hasManyThrough(PhotoGallery::class, TaggedCategory::class, 'category_id', 'id', 'id', 'parentable_id')
            ->where('parentable_type', PhotoGallery::class);
    }
}
