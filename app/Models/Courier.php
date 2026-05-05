<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Courier extends Model
{
    use HasFactory;

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