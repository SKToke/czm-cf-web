<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BkashAgreement extends Model
{
    protected $fillable = [
        'user_id',
        'agreement_id',
        'payer_reference',
        'wallet',
        'status',
    ];
}
