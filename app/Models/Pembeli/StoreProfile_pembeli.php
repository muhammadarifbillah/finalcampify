<?php

namespace App\Models\Pembeli;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Store;

class StoreProfile_pembeli extends Store
{
    protected $table = 'stores';

    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_name',
        'nama_toko',
        'description',
        'deskripsi',
        'address',
        'alamat',
        'image',
        'logo',
    ];

    public function getStoreNameAttribute()
    {
        return $this->attributes['nama_toko'] ?? null;
    }

    public function setStoreNameAttribute($value): void
    {
        $this->attributes['nama_toko'] = $value;
    }

    public function getDescriptionAttribute()
    {
        return $this->attributes['deskripsi'] ?? null;
    }

    public function setDescriptionAttribute($value): void
    {
        $this->attributes['deskripsi'] = $value;
    }

    public function getAddressAttribute()
    {
        return $this->attributes['alamat'] ?? null;
    }

    public function setAddressAttribute($value): void
    {
        $this->attributes['alamat'] = $value;
    }

    public function getImageAttribute()
    {
        return $this->attributes['logo'] ?? null;
    }

    public function setImageAttribute($value): void
    {
        $this->attributes['logo'] = $value;
    }

    public function user()
    {
        return $this->belongsTo(User_pembeli::class, 'user_id');
    }
}
