<?php

namespace App\Models\SellerModels;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;

class Order_seller extends Order
{
    protected $table = 'orders';

    protected $appends = ['qty', 'resi'];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function firstDetail()
    {
        return $this->hasOne(OrderDetail::class, 'order_id')->oldestOfMany();
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public function rental()
    {
        return $this->hasOne(Rental_seller::class, 'order_id');
    }

    public function product()
    {
        return $this->hasOneThrough(
            Product_seller::class,
            OrderDetail::class,
            'order_id',
            'id',
            'id',
            'product_id'
        );
    }

    public function getQtyAttribute()
    {
        return $this->details->sum('qty');
    }

    public function getResiAttribute()
    {
        return $this->no_resi;
    }

    public function setResiAttribute($value): void
    {
        $this->attributes['no_resi'] = $value;
    }
}
