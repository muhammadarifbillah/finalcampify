<?php

namespace App\Models\Pembeli;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental_pembeli extends Model
{
    protected $table = 'rentals';

    use HasFactory;

    protected $fillable = ['user_id', 'product_id', 'order_id', 'start_date', 'end_date', 'duration', 'price', 'status'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User_pembeli::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product_pembeli::class, 'product_id');
    }

    public function order()
    {
        return $this->belongsTo(Order_pembeli::class, 'order_id');
    }

    public function returnRequest()
    {
        return $this->hasOne(Return_pembeli::class, 'rental_id');
    }
}
