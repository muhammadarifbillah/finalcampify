<?php

namespace App\Models\SellerModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SellerModels\ProductRating_seller;
use App\Models\User;
use App\Models\Store;

class Product_seller extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'user_id',
        'store_id',
        'name',
        'nama_produk',
        'description',
        'deskripsi',
        'price',
        'harga',
        'category',
        'kategori',
        'jenis_produk',
        'is_rental',
        'buy_price',
        'rent_price',
        'stock',
        'stok',
        'image',
        'gambar',
        'status',
        'flag_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function getNamaProdukAttribute()
    {
        return $this->attributes['nama_produk'] ?? $this->attributes['name'] ?? null;
    }

    public function setNamaProdukAttribute($value)
    {
        $this->attributes['nama_produk'] = $value;
        $this->attributes['name'] = $value;
    }

    public function getHargaAttribute()
    {
        return $this->attributes['harga'] ?? $this->attributes['price'] ?? $this->attributes['buy_price'] ?? 0;
    }

    public function setHargaAttribute($value)
    {
        $this->attributes['harga'] = $value;
        $this->attributes['price'] = $value;
    }

    public function getStokAttribute()
    {
        return $this->attributes['stok'] ?? $this->attributes['stock'] ?? 0;
    }

    public function setStokAttribute($value)
    {
        $this->attributes['stok'] = $value;
        $this->attributes['stock'] = $value;
    }

    public function ratings()
    {
        return $this->hasMany(ProductRating_seller::class, 'product_id');
    }

    public function orders()
    {
        return $this->hasMany(\App\Models\OrderDetail::class, 'product_id', 'id');
    }

    public function averageRating()
    {
        return $this->ratings()->avg('rating') ?? 0;
    }

    public function ratingCount()
    {
        return $this->ratings()->count();
    }

    /**
     * Get the image URL for the product
     * Handles both old paths (with 'products/' or 'storage/') and new filename-only format
     */
    public function getImageUrlAttribute()
    {
        $imageField = $this->image ?: $this->gambar;
        
        if (!$imageField) {
            return null;
        }

        // If it's already a full URL, return as-is
        if (filter_var($imageField, FILTER_VALIDATE_URL)) {
            return $imageField;
        }

        // If it contains 'products/' or 'storage/', it's the old format - extract filename
        if (strpos($imageField, 'products/') !== false || strpos($imageField, 'storage/') !== false) {
            $filename = basename($imageField);
            return asset('assets/images/' . $filename);
        }

        // If it's just a filename (new format), serve from assets/images
        return asset('assets/images/' . $imageField);
    }
}
