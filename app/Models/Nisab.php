<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nisab extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'gold_value', 'silver_value', 'nisab_update_date'
    ];
    protected $dates = ['nisab_update_date'];

    public function getGoldPriceAttribute()
    {
        return $this->gold_value * 85;
    }

    public function getSilverPriceAttribute()
    {
        return $this->silver_value * 595;
    }
}
