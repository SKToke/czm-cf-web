<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BkashPayment extends Model
{
    protected $fillable = [
        'payment_id',
        'trx_id',
        'agreement_id',
        'invoice',
        'amount',
        'status',
    ];
}
