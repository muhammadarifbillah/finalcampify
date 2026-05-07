<?php

namespace App\Models\SellerModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Rental_seller extends Model
{
    use HasFactory;

    protected $table = 'rentals';

    protected $fillable = [
        'user_id', 'product_id', 'tanggal_mulai', 'tanggal_selesai', 
        'total_harga', 'status', 'catatan', 'price', 'duration'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product() {
        return $this->belongsTo(Product_seller::class);
    }
}