<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends AbstractModel
{
    use HasFactory;
    use softDeletes;

    protected $fillable = [
        'name',
        'image',
        'self_designation',
        'description',
        'contact_number',
        'facebook_link',
        'linkedin_link',

    ];

    public function committeeMembers(): HasMany
    {
        return $this->hasMany(CommitteeMember::class);
    }

    public function committees(): HasManyThrough
    {
        return $this->hasManyThrough(Committee::class, CommitteeMember::class);
    }

    public function getImage(): string
    {
        return asset($this->getDynamicImageUrl($this->image));
    }
}


