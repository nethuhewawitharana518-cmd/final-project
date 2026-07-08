<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'customer_id', 'business_id', 'reservation_code', 'pickup_time',
        'subtotal', 'platform_commission', 'business_earnings',
        'loyalty_points_used', 'loyalty_discount', 'total_amount', 'status', 'notes',
        'delivery_method', 'delivery_address', 'delivery_fee',
        'delivery_latitude', 'delivery_longitude',
    ];

    protected $casts = [
        'pickup_time'         => 'datetime',
        'subtotal'            => 'float',
        'platform_commission' => 'float',
        'business_earnings'   => 'float',
        'loyalty_discount'    => 'float',
        'total_amount'        => 'float',
        'delivery_fee'        => 'float',
        'delivery_latitude'   => 'float',
        'delivery_longitude'  => 'float',
    ];

    // ─── Relationships ────────────────────────────────────────────────
    public function customer()   { return $this->belongsTo(User::class, 'customer_id'); }
    public function business()   { return $this->belongsTo(Business::class); }
    public function items()      { return $this->hasMany(OrderItem::class); }
    public function payment()    { return $this->hasOne(Payment::class); }
    public function qrCode()     { return $this->hasOne(QrCode::class); }
    public function commission() { return $this->hasOne(Commission::class); }
    public function review()     { return $this->hasOne(Review::class); }

    // ─── Status Helpers ───────────────────────────────────────────────
    public function isPaid(): bool      { return $this->status === 'paid'; }
    public function isCollected(): bool { return $this->status === 'collected'; }
    public function isCancelled(): bool { return $this->status === 'cancelled'; }

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'pending'   => 'warning',
            'confirmed' => 'info',
            'paid'      => 'primary',
            'collected' => 'success',
            'cancelled' => 'danger',
            'expired'   => 'secondary',
            default     => 'light',
        };
    }

    public function getTotalPriceAttribute(): float
    {
        return $this->total_amount;
    }

    // ─── Scopes ──────────────────────────────────────────────────────
    public function scopePaid($query)      { return $query->where('status', 'paid'); }
    public function scopeCollected($query) { return $query->where('status', 'collected'); }
    public function scopePending($query)   { return $query->where('status', 'pending'); }
}
