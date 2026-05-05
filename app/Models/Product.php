<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'seller_id',
        'nama_produk',
        'harga',
        'stok',
        'deskripsi',
        'kategori_id',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    public function getNameAttribute()
    {
        return $this->attributes['nama_produk'] ?? null;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['nama_produk'] = $value;
    }

    public function getPriceAttribute()
    {
        return $this->attributes['harga'] ?? null;
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['harga'] = $value;
    }

    public function getDescriptionAttribute()
    {
        return $this->attributes['deskripsi'] ?? null;
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['deskripsi'] = $value;
    }

    public function getStockAttribute()
    {
        return $this->attributes['stok'] ?? null;
    }

    public function setStockAttribute($value)
    {
        $this->attributes['stok'] = $value;
    }
}