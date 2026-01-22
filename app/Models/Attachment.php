<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['parentable_type', 'parentable_id', 'title', 'download_count', 'file'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($attachment) {
            $attachment->download_count = $attachment->download_count ?? 0;
            if ($attachment->file && !$attachment->title) {
                $attachment->title = pathinfo($attachment->file, PATHINFO_FILENAME);
            }
        });

        static::updating(function ($attachment) {
            if (empty($attachment->file) || $attachment->file === '0') {
                $attachment->file = $attachment->getOriginal('file');
            }
        });
    }

    public function parentable()
    {
        return $this->morphTo();
    }
}
