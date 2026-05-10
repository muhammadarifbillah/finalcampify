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
        'user_id',
        'store_id',
        'name',
        'nama_produk',
        'category',
        'kategori',
        'description',
        'deskripsi',
        'price',
        'harga',
        'buy_price',
        'rent_price',
        'status',
        'flag_reason',
        'reviewed_by',
        'reviewed_at',
        'is_rental',
        'jenis_produk',
        'rating',
        'reviews_count',
        'image',
        'gambar',
        'stock',
        'stok',
        'kategori_id',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'is_rental' => 'boolean',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
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
        return $this->attributes['name'] ?? $this->attributes['nama_produk'] ?? null;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['nama_produk'] = $value;
    }

    public function getPriceAttribute()
    {
        return $this->attributes['price'] ?? $this->attributes['harga'] ?? $this->attributes['buy_price'] ?? null;
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = $value;
        $this->attributes['harga'] = $value;
    }

    public function getDescriptionAttribute()
    {
        return $this->attributes['description'] ?? $this->attributes['deskripsi'] ?? null;
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = $value;
        $this->attributes['deskripsi'] = $value;
    }

    public function getStockAttribute()
    {
        return $this->attributes['stock'] ?? $this->attributes['stok'] ?? null;
    }

    public function setStockAttribute($value)
    {
        $this->attributes['stock'] = $value;
        $this->attributes['stok'] = $value;
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'product_id', 'id');
    }

    public function couriers()
    {
        return $this->belongsToMany(Courier::class, 'product_courier');
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function sellerUserId(): ?int
    {
        return $this->seller_id
            ?? $this->user_id
            ?? $this->store?->user_id;
    }

    public static function flagReasonsFor(array $data): array
    {
        $text = strtolower(($data['name'] ?? $data['nama_produk'] ?? '') . ' ' . ($data['description'] ?? $data['deskripsi'] ?? ''));
        $price = (int) ($data['price'] ?? $data['harga'] ?? $data['buy_price'] ?? $data['rent_price'] ?? 0);
        $badWords = ['palsu', 'penipuan', 'judi', 'narkoba', 'senjata'];
        $reasons = [];

        if ($price > 50000000) {
            $reasons[] = 'Harga tidak wajar';
        }

        foreach ($badWords as $word) {
            if (str_contains($text, $word)) {
                $reasons[] = 'Kata terlarang: ' . $word;
            }
        }

        return $reasons;
    }

    /**
     * Get the image URL for the product
     * Handles both old paths (with 'products/', 'storage/', 'ktp_uploads/', etc.) and new filename-only format
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

        // If it contains '/', it's the old format - extract filename
        if (strpos($imageField, '/') !== false) {
            $filename = basename($imageField);
            return asset('assets/images/' . $filename);
        }

        // If it's just a filename (new format), serve from assets/images
        return asset('assets/images/' . $imageField);
    }
}
