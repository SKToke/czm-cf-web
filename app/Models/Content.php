<?php

namespace App\Models;
use App\Enums\ContentTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Content extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

    protected $fillable = ['name', 'content_type', 'slug'];

    public const CONTENT_TYPES = [
        'blog' => 1,
        'news' => 2,
        'story' => 3,
        'quranic_verse' => 4,
        'sadaqah' => 5,
        'cash_waqf' => 6,
        'qard_al_hasan' => 7,
    ];

    protected $casts = [
        'content_type' => ContentTypeEnum::class,
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    // Relationships
    public function contentsections()
    {
        return $this->hasMany(ContentSection::class);
    }

    public function taggedCategories(): MorphMany
    {
        return $this->morphMany(TaggedCategory::class, 'parentable');
    }

    public function categories(): HasManyThrough
    {
        return $this->hasManyThrough(Category::class, TaggedCategory::class, 'parentable_id', 'id', 'id', 'category_id')
            ->where('parentable_type', Content::class);
    }

    // Correctly named local scope for Laravel's automatic scope resolution
    public function scopeBlogs($query)
    {
        return $query->where('content_type', self::CONTENT_TYPES['blog']);
    }


    public function scopeNews($query)
    {
        return $query->where('content_type', self::CONTENT_TYPES['news']);
    }

    public function scopeStories($query)
    {
        return $query->where('content_type', self::CONTENT_TYPES['story']);
    }

    public function scopeQuranicVerse($query)
    {
        return $query->where('content_type', self::CONTENT_TYPES['quranic_verse']);
    }
    public function scopeSadaqah($query)
    {
        return $query->where('content_type', self::CONTENT_TYPES['sadaqah']);
    }
    public function scopeCashWaqf($query)
    {
        return $query->where('content_type', self::CONTENT_TYPES['cash_waqf']);
    }
    public function scopeQardAlHasan($query)
    {
        return $query->where('content_type', self::CONTENT_TYPES['qard_al_hasan']);
    }
}
