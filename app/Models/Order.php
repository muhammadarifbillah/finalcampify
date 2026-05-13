<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'receiver_name',
        'total',
        'shipping_address',
        'shipping_city',
        'shipping_district',
        'shipping_postal_code',
        'shipping_phone',
        'metode_pembayaran',
        'status',
        'kurir',
        'no_resi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public function returns()
    {
        return $this->hasMany(ReturnEscrow::class, 'order_id');
    }
}
