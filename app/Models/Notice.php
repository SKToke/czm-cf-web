<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Attachment;

class Notice extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'published_date',
    ];

    public static $rules = [
        'title' => 'required|max:120',
        'published_date' => 'required|date',
        'description' => 'max:300',
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($notice) {
            $notice->attachments->each(function ($attachment) {
                $attachment->delete();
            });
        });
    }


    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'parentable');
    }

    public function hasAttachments()
    {
        return $this->attachments()->count() > 0;
    }

    public function hasBody()
    {
        return $this->description || $this->attachments()->exists();
    }
}
