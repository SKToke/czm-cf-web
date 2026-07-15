<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BkashSingleAgreement extends Model
{
    protected $table = 'bkash_single_agreements';

    protected $fillable = [
        'user_id',
        'agreement_id',
        'payer_reference',
        'wallet',
        'status',
    ];
}
