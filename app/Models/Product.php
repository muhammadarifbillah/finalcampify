<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Store;
use App\Models\Courier;

class Product extends Model
{
    protected $fillable = [
        'store_id',
        'name',
        'price',
        'status',      // pending | approved | rejected
        'is_rental'    // true / false
    ];

    // 🔥 RELASI KE TOKO
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function couriers()
    {
        return $this->belongsToMany(Courier::class, 'product_courier')->withTimestamps();
    }
}