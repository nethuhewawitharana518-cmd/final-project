<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DirectoryPlace extends Model
{
    protected $fillable = [
        'name', 'category', 'address', 'latitude', 'longitude', 'osm_id',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
    ];
}
