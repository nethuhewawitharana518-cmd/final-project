<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'reservation_id', 'food_id', 'food_name', 'quantity', 'unit_price', 'total_price',
    ];

    protected $casts = [
        'unit_price'  => 'float',
        'total_price' => 'float',
        'quantity'    => 'integer',
    ];

    public function getPriceAttribute(): float
    {
        return $this->unit_price;
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }
}
