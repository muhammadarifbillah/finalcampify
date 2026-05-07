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
<<<<<<< HEAD
        'user_id', 'product_id', 'tanggal_mulai', 'tanggal_selesai', 
        'total_harga', 'status', 'catatan', 'price', 'duration'
=======
        'user_id', 'product_id', 'order_id', 'start_date', 'end_date', 
        'duration', 'price', 'status', 'catatan'
>>>>>>> 2d40e9637aaa8a3407944b439b4b9a23b1eef251
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product() {
        return $this->belongsTo(Product_seller::class);
    }

    public function order() {
        return $this->belongsTo(\App\Models\Order::class, 'order_id');
    }
}