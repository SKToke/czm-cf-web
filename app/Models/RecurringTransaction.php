<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecurringTransaction extends Model
{
    protected $fillable = [
        'recurring_subscription_id',
        'payment_id',
        'tran_id',
        'amount',
        'currency',
        'payment_status',
        'gateway_response',
        'paid_at',
        'refund_trx_id',
        'refunded_amount',
        'refunded_at',
        'refund_reason',
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
        'refunded_amount' => 'decimal:2',
    ];

    public function subscription()
    {
        return $this->belongsTo(RecurringSubscription::class, 'recurring_subscription_id');
    }
}
