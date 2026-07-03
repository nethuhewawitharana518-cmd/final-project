<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyPoint extends Model
{
    const UPDATED_AT = null;
    protected $fillable = [
        'user_id', 'reservation_id', 'points_earned', 'points_redeemed',
        'balance', 'tier', 'transaction_type', 'description',
    ];

    protected $casts = [
        'points_earned'   => 'integer',
        'points_redeemed' => 'integer',
        'balance'         => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
