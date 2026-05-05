<?php

namespace App\Models\SellerModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Store;
use App\Models\User;

class StoreProfile_seller extends Store
{
    use HasFactory;

    protected $table = 'stores';
    
    protected $fillable = [
        'user_id', 'nama_toko', 'deskripsi', 'alamat', 'no_telp', 'logo', 'banner'
    ];

    public function getNoTelpAttribute()
    {
        return $this->attributes['no_telp'] ?? $this->user?->phone;
    }

    public function setNoTelpAttribute($value): void
    {
        $this->attributes['no_telp'] = $value;
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
