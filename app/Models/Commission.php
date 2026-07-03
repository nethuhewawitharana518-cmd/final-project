<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    const UPDATED_AT = null;
    protected $fillable = [
        'reservation_id', 'business_id', 'sale_amount',
        'commission_rate', 'commission_amount', 'business_earnings',
        'status', 'settled_at',
    ];

    protected $casts = [
        'sale_amount'       => 'float',
        'commission_rate'   => 'float',
        'commission_amount' => 'float',
        'business_earnings' => 'float',
        'settled_at'        => 'datetime',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
