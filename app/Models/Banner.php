<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Banner extends AbstractModel
{
    protected $fillable = ['key'];

    public static function getBannerFor($key)
    {
        return self::where('key', $key)->first();
    }

    public function getImageUrl(): string
    {
        return asset($this->getDynamicImageUrl($this->image));
    }
    public function hasImage(): bool
    {
        return !empty($this->image);
    }
}
