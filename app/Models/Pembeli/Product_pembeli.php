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

    public function getImageUrlAttribute()
    {
        $imageField = $this->attributes['image'] ?? $this->attributes['gambar'] ?? null;

        if (!$imageField) {
            return null;
        }

        if (filter_var($imageField, FILTER_VALIDATE_URL)) {
            return $imageField;
        }

        $imageField = str_replace('\\', '/', $imageField);
        $filename = basename($imageField);

        if (str_starts_with($imageField, 'assets/images/')) {
            return asset($imageField);
        }

        if (str_starts_with($imageField, 'storage/')) {
            return asset($imageField);
        }

        if (str_starts_with($imageField, 'public/')) {
            return asset('storage/' . substr($imageField, strlen('public/')));
        }

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($imageField)) {
            return asset('storage/' . $imageField);
        }

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists('products/' . $filename)) {
            return asset('storage/products/' . $filename);
        }

        if (file_exists(public_path('assets/images/' . $filename))) {
            return asset('assets/images/' . $filename);
        }

        return asset('assets/images/' . $filename);
    }
}
