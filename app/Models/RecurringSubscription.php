<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecurringSubscription extends Model
{
    protected $fillable = [
        'donor_id',
        'refer',
        'subscription_id',
        'amount',
        'currency',
        'frequency_type',
        'billing_day',
        'status',
        'started_at',
        'next_billing_at',
        'paused_at',
        'resumed_at',
        'cancel_requested_at',
        'cancelled_at',
        'last_tran_id',
        'last_payment_at',
        'last_payment_status',
    ];

    protected $dates = [
        'started_at',
        'next_billing_at',
        'paused_at',
        'resumed_at',
        'cancel_requested_at',
        'cancelled_at',
        'last_payment_at',
    ];

    public function donor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function transactions()
    {
        return $this->hasMany(RecurringTransaction::class);
    }
}
