<?php

namespace App\Models\Recurring;

use Illuminate\Database\Eloquent\Model;

class RecurringTransaction extends Model
{
    protected $fillable = [
        'recurring_subscription_id',
        'tran_id',
        'amount',
        'currency',
        'payment_status',
        'gateway_response',
        'paid_at',
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function subscription()
    {
        return $this->belongsTo(RecurringSubscription::class, 'recurring_subscription_id');
    }
}
