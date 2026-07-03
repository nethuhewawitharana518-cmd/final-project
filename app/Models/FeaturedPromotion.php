<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeaturedPromotion extends Model
{
    const UPDATED_AT = null;
    protected $fillable = [
        'business_id', 'promotion_type', 'fee_paid',
        'start_date', 'end_date', 'status', 'payment_id',
    ];

    protected $casts = [
        'fee_paid'   => 'float',
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
