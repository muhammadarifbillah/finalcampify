<?php

namespace App\Models\SellerModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ProductRating_seller extends Model
{
    use HasFactory;

    protected $table = 'product_ratings';

    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'comment',
        'ulasan',
    ];

    public function product()
    {
        return $this->belongsTo(Product_seller::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUlasanAttribute()
    {
        return $this->attributes['ulasan'] ?? $this->attributes['comment'] ?? null;
    }

    public function setUlasanAttribute($value): void
    {
        $this->attributes['ulasan'] = $value;
        $this->attributes['comment'] = $value;
    }

    public static function getAverageRating($productId)
    {
        return self::where('product_id', $productId)->avg('rating') ?? 0;
    }

    public static function getRatingCount($productId)
    {
        return self::where('product_id', $productId)->count();
    }
}
