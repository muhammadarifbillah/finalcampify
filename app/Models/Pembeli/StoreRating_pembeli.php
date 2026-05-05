<?php
namespace App\Models\Pembeli;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreRating_pembeli extends Model
{
    use HasFactory;

    protected $table = 'store_ratings';
    protected $fillable = ['user_id', 'store_id', 'order_id', 'rating', 'comment', 'ulasan'];

    public function user()
    {
        return $this->belongsTo(User_pembeli::class, 'user_id');
    }

    public function store()
    {
        // Menyambung ke tabel users yang memiliki role seller (karena store_id referensi ke users)
        return $this->belongsTo(User_pembeli::class, 'store_id');
    }

    public function order()
    {
        return $this->belongsTo(Order_pembeli::class, 'order_id');
    }
}
