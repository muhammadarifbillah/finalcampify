<?php

namespace App\Models\SellerModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SellerModels\ProductRating_seller;
use App\Models\User;

class Product_seller extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'user_id',
        'nama_produk',
        'deskripsi',
        'harga',
        'kategori',
        'jenis_produk',
        'stok',
        'gambar',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function ratings()
    {
        return $this->hasMany(ProductRating_seller::class, 'product_id');
    }

    public function averageRating()
    {
        return $this->ratings()->avg('rating') ?? 0;
    }

    public function ratingCount()
    {
        return $this->ratings()->count();
    }
}
