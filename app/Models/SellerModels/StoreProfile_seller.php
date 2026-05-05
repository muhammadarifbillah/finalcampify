<?php

namespace App\Models\SellerModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class StoreProfile_seller extends Model
{
    use HasFactory;

    protected $table = 'store_profiles';
    
    protected $fillable = [
        'user_id', 'nama_toko', 'deskripsi', 'alamat', 'no_telp', 'logo', 'banner'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}