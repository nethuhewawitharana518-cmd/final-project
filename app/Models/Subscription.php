<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'business_id', 'plan_type', 'price', 'upload_limit',
        'start_date', 'end_date', 'status', 'payment_id',
    ];

    protected $casts = [
        'price'        => 'float',
        'upload_limit' => 'integer',
        'start_date'   => 'date',
        'end_date'     => 'date',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->end_date->isFuture();
    }
}
