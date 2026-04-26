<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Product;

class Store extends Model
{
    protected $fillable = [
        'user_id',
        'nama_toko',
        'status',        // aktif | banned | nonaktif
        'last_active',
        'alasan_ban'
    ];

    // 🔥 RELASI KE USER
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔥 RELASI KE PRODUK
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}