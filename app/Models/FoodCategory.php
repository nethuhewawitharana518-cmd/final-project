<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodCategory extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'icon', 'slug', 'is_active'];

    public function foods()
    {
        return $this->hasMany(Food::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
