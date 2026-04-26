<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    protected $fillable = [
        'name',
        'service',
        'estimate',
        'price',
        'status'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_courier')->withTimestamps();
    }
}