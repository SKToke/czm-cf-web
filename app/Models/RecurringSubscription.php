<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecurringSubscription extends Model
{
    protected $fillable = [
        'payment_gateway',
        'donor_id',
        'refer',
        'amount',
        'currency',
        'frequency_type',
        'billing_day',
        'status',
        'subscription_id',
        'subscription_id_onreq',
        'subscription_status_onreq',
        'sessionkey_onreq',
        'started_at',
        'next_billing_at',
        'paused_at',
        'resumed_at',
        'cancel_requested_at',
        'cancelled_at',
        'last_tran_id',
        'last_payment_at',
        'last_payment_status',

        'payer_number',
        'expires_at',
        'deduction_failure_count',
        'cancelled_by',

        'val_id',
        'bank_tran_id',
        'card_issuer_bank',
        'card_no',
        'card_brand',
        'card_sub_brand',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'next_billing_at' => 'datetime',
        'expires_at' => 'datetime',
        'paused_at' => 'datetime',
        'resumed_at' => 'datetime',
        'cancel_requested_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'last_payment_at' => 'datetime',
        'deduction_failure_count' => 'integer',
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
