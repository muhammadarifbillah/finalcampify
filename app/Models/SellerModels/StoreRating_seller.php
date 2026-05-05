<?php

namespace App\Models\SellerModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class StoreRating_seller extends Model
{
    use HasFactory;

    protected $table = 'store_ratings';

    protected $fillable = [
        'store_id',
        'user_id',
        'rating',
        'ulasan',
    ];

    public function store()
    {
        return $this->belongsTo(User_seller::class, 'store_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getAverageRating($storeId)
    {
        return self::where('store_id', $storeId)->avg('rating') ?? 0;
    }

    public static function getRatingCount($storeId)
    {
        return self::where('store_id', $storeId)->count();
    }
}