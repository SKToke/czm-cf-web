<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommitteeMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'committee_id',
        'member_id',
        'position',
        'designation',
    ];

    public function committee()
    {
        return $this->belongsTo(Committee::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
