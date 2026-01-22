<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Committee extends Model
{
    use HasFactory;
    use softDeletes;

    protected $fillable = [
        'name',
        'description',
        'position',
    ];

    public function committeeMembers(): HasMany
    {
        return $this->hasMany(CommitteeMember::class)->orderBy('position');
    }

    public function members(): HasManyThrough
    {
        return $this->hasManyThrough(Member::class, CommitteeMember::class);
    }
}
