<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    protected $table = 'foods';
    protected $fillable = [
        'business_id', 'category_id', 'name', 'description', 'image',
        'original_price', 'discount_price', 'discount_percentage',
        'quantity', 'available_quantity', 'expiry_datetime',
        'status', 'ai_risk_level', 'ai_recommended_discount',
        'is_featured', 'views_count',
    ];

    protected $casts = [
        'expiry_datetime'        => 'datetime',
        'original_price'         => 'float',
        'discount_price'         => 'float',
        'is_featured'            => 'boolean',
        'ai_recommended_discount'=> 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────────────
    public function business()  { return $this->belongsTo(Business::class); }
    public function category()  { return $this->belongsTo(FoodCategory::class, 'category_id'); }
    public function orderItems(){ return $this->hasMany(OrderItem::class); }

    // ─── Computed Attributes ──────────────────────────────────────────
    public function getHoursRemainingAttribute(): float
    {
        return max(0, now()->diffInMinutes($this->expiry_datetime, false) / 60);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_datetime->isPast();
    }

    public function getSavingsAmountAttribute(): float
    {
        return $this->original_price - $this->discount_price;
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'active'   => 'success',
            'sold_out' => 'secondary',
            'expired'  => 'danger',
            'hidden'   => 'warning',
            default    => 'light',
        };
    }

    public function getAiRiskBadgeColorAttribute(): string
    {
        return match($this->ai_risk_level) {
            'high'   => 'danger',
            'medium' => 'warning',
            'low'    => 'success',
            default  => 'secondary',
        };
    }

    // ─── Scopes ──────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('expiry_datetime', '>', now())
                     ->where('available_quantity', '>', 0);
    }

    public function scopeExpiringSoon($query, int $hours = 6)
    {
        return $query->where('expiry_datetime', '<=', now()->addHours($hours));
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeHighRisk($query)
    {
        return $query->where('ai_risk_level', 'high');
    }
}
