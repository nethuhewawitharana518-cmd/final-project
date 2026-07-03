<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'reservation_id', 'user_id', 'amount', 'gateway',
        'transaction_id', 'payment_intent_id',
        'card_brand', 'card_last4', 'card_country', 'card_funding',
        'currency', 'status', 'gateway_response',
        'failure_code', 'failure_message', 'paid_at',
    ];

    protected $casts = [
        'amount'           => 'float',
        'gateway_response' => 'array',
        'paid_at'          => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────────────
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────
    public function getCardDisplayAttribute(): string
    {
        if ($this->card_brand && $this->card_last4) {
            return ucfirst($this->card_brand) . ' ···· ' . $this->card_last4;
        }
        return ucfirst($this->gateway ?? 'Unknown');
    }

    public function getCardBrandIconAttribute(): string
    {
        return match (strtolower($this->card_brand ?? '')) {
            'visa'       => 'fa-brands fa-cc-visa text-primary',
            'mastercard' => 'fa-brands fa-cc-mastercard text-danger',
            'amex'       => 'fa-brands fa-cc-amex text-info',
            'discover'   => 'fa-brands fa-cc-discover text-warning',
            default      => 'fa-solid fa-credit-card text-secondary',
        };
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }
}
