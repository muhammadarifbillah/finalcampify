<?php
namespace App\Models\Pembeli;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class Product_pembeli extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = ['name','category','description','buy_price','rent_price','rating','reviews_count','image','stock','status'];

    protected static function booted(): void
    {
        static::addGlobalScope('approved', function (Builder $builder) {
            $builder->where('status', 'approved');
        });
    }

    public function productRatings()
    {
        return $this->hasMany(ProductRating_pembeli::class, 'product_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'user_id', 'user_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sellerUserId(): ?int
    {
        return $this->seller_id
            ?? $this->user_id
            ?? $this->store?->user_id;
    }

    /**
     * Aliasing accessors for English/Indonesian consistency
     */
    public function getNameAttribute()
    {
        return $this->attributes['name'] ?? $this->attributes['nama_produk'] ?? null;
    }

    public function getPriceAttribute()
    {
        return $this->attributes['price'] ?? $this->attributes['harga'] ?? $this->attributes['buy_price'] ?? null;
    }

    public function getDescriptionAttribute()
    {
        return $this->attributes['description'] ?? $this->attributes['deskripsi'] ?? null;
    }

    public function getStockAttribute()
    {
        return $this->attributes['stock'] ?? $this->attributes['stok'] ?? null;
    }

    /**
     * Get the image URL for the product
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

        $filename = basename($imageField);

        // Check if it exists in storage products folder
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists('products/' . $filename)) {
            return asset('storage/products/' . $filename);
        }

        // Check if it's in assets/images
        if (file_exists(public_path('assets/images/' . $filename))) {
            return asset('assets/images/' . $filename);
        }

        // Fallback to serving through the /images/{path} route if it contains path segments
        if (strpos($imageField, '/') !== false) {
             return asset('images/' . ltrim($imageField, '/'));
        }

        // Final fallback to assets/images
        return asset('assets/images/' . $filename);
    }
}
