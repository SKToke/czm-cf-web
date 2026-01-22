<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaggedCategory extends Model
{
    protected $fillable = [
        'category_id',
        'parentable_id',
        'parentable_type',
        'deleted_at',
    ];

    public function parentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
