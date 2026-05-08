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
        'user_id',
        'product_id',
        'order_id',
        'start_date',
        'end_date',
        'total_price',
        'duration',
        'price',
        'status',
        'catatan',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function returnRequest()
    {
        return $this->hasOne(Return_seller::class, 'rental_id');
    }

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