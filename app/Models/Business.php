<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'business_name', 'business_type', 'address', 'latitude', 'longitude',
        'reg_number', 'phone', 'email', 'description', 'logo', 'documents',
        'status', 'rejection_reason', 'is_featured', 'featured_fee',
        'reg_fee_paid', 'reg_fee_paid_at',
    ];

    protected $casts = [
        'documents'      => 'array',
        'is_featured'    => 'boolean',
        'reg_fee_paid'   => 'boolean',
        'reg_fee_paid_at'=> 'datetime',
        'latitude'       => 'float',
        'longitude'      => 'float',
    ];

    protected $attributes = [
        'latitude' => 8.5755,
        'longitude' => 81.2285,
    ];

    // ─── Status Helpers ───────────────────────────────────────────────
    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isApproved(): bool  { return $this->status === 'approved'; }
    public function isRejected(): bool  { return $this->status === 'rejected'; }
    public function isSuspended(): bool { return $this->status === 'suspended'; }

    // ─── Subscription ─────────────────────────────────────────────────
    public function hasActiveSubscription(): bool
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('end_date', '>=', now()->toDateString())
            ->exists();
    }

    public function activeSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('end_date', '>=', now()->toDateString())
            ->latest()
            ->first();
    }

    public function canUploadFood(): bool
    {
        $sub = $this->activeSubscription();
        if (!$sub) return false;
        if ($sub->upload_limit === -1) return true;

        $uploaded = $this->foods()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return $uploaded < $sub->upload_limit;
    }

    // ─── Relationships ────────────────────────────────────────────────
    public function user()          { return $this->belongsTo(User::class); }
    public function subscriptions() { return $this->hasMany(Subscription::class); }
    public function foods()         { return $this->hasMany(Food::class); }
    public function reservations()  { return $this->hasMany(Reservation::class); }
    public function commissions()   { return $this->hasMany(Commission::class); }
    public function featuredPromotions() { return $this->hasMany(FeaturedPromotion::class); }

    // ─── Scopes ──────────────────────────────────────────────────────
    public function scopeApproved($query)  { return $query->where('status', 'approved'); }
    public function scopeFeatured($query)  { return $query->where('is_featured', true); }
    public function scopeByType($query, $type) { return $query->where('business_type', $type); }

    // ─── Earnings ─────────────────────────────────────────────────────
    public function getTotalEarnings(): float
    {
        return (float) $this->commissions()->sum('business_earnings');
    }

    public function getMonthlyEarnings(): float
    {
        return (float) $this->commissions()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('business_earnings');
    }

    // ─── Reviews & Badges ──────────────────────────────────────────────
    public function reviews() { return $this->hasMany(Review::class); }

    public function getAverageRatingAttribute(): float
    {
        return round((float) $this->reviews()->avg('rating'), 1);
    }

    public function getReviewCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    public function getBadgeAttribute(): ?string
    {
        // Calculate Top 3 businesses based on average rating, then by review count to break ties
        $topBusinesses = \Illuminate\Support\Facades\Cache::remember('top_3_businesses', 60, function () {
            return self::withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->having('reviews_count', '>', 0)
                ->orderByDesc('reviews_avg_rating')
                ->orderByDesc('reviews_count')
                ->limit(3)
                ->pluck('id')
                ->toArray();
        });

        $rank = array_search($this->id, $topBusinesses);

        if ($rank === 0) return '1st';
        if ($rank === 1) return '2nd';
        if ($rank === 2) return '3rd';

        return null;
    }
}
