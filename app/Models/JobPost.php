<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes; // If you're using soft deletes
use Carbon\Carbon;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class JobPost extends AbstractModel
{
    use HasFactory, SoftDeletes, HasSlug;

    protected $dates = ['opening_date', 'closing_date'];
    protected $fillable = ['title', 'job_nature', 'company_name', 'opening_date', 'closing_date', 'location', 'slug']; // Fillable attributes for mass assignment

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class);
    }
    public function scopeNotExpired($query)
    {
        return $query->where('closing_date', '>=', Carbon::today());
    }

    public function getLogoUrl(): string
    {
        return asset($this->getDynamicImageUrl($this->logo));
    }
}
