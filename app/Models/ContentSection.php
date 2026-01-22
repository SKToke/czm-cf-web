<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentSection extends AbstractModel
{
    use SoftDeletes;

    protected $fillable = ['title', 'description', 'position', 'image'];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function getImage(): string
    {
        return asset($this->getDynamicImageUrl($this->image));
    }
}
