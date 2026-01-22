<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class HeroSection extends AbstractModel
{
    use SoftDeletes;

    protected $fillable = [
        'photo',
        'description',
        'active',
        'deleted_at',
    ];

    public static function getActives(): mixed
    {
        return HeroSection::where('active', true)->get();
    }

    public function getPhoto(): string
    {
        return asset($this->getDynamicImageUrl($this->photo));
    }

    public static function anyActive(): bool
    {
        return HeroSection::where('active', true)->exists();
    }
}
