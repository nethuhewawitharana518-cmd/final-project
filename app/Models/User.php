<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'avatar', 'status', 'home_address',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // ─── Role Helpers ────────────────────────────────────────────────
    public function isAdmin(): bool          { return $this->role === 'admin'; }
    public function isBusinessOwner(): bool  { return $this->role === 'business_owner'; }
    public function isCustomer(): bool       { return $this->role === 'customer'; }
    public function isActive(): bool         { return $this->status === 'active'; }

    // ─── Relationships ────────────────────────────────────────────────
    public function business()
    {
        return $this->hasOne(Business::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'customer_id');
    }

    public function loyaltyPoints()
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // ─── Loyalty Points Summary ───────────────────────────────────────
    public function getTotalLoyaltyPoints(): int
    {
        return $this->loyaltyPoints()->sum('points_earned')
             - $this->loyaltyPoints()->sum('points_redeemed');
    }

    public function getLoyaltyTier(): string
    {
        $points = $this->getTotalLoyaltyPoints();
        if ($points >= 500) return 'gold';
        if ($points >= 100) return 'silver';
        return 'bronze';
    }

    public function getUnreadNotificationsCount(): int
    {
        return $this->notifications()->where('is_read', false)->count();
    }
}
