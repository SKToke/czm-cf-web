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

        'val_id',
        'bank_tran_id',
        'card_issuer_bank',
        'card_no',
        'card_brand',
        'card_sub_brand',
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
