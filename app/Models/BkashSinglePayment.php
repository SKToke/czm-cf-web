<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BkashSinglePayment extends Model
{
    protected $table = 'bkash_single_payments';

    protected $fillable = [
        'payment_id',
        'trx_id',
        'agreement_id',
        'invoice',
        'amount',
        'status',
    ];
}
