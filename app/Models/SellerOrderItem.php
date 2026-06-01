<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_order_id',
        'order_detail_id',
        'product_id',
        'qty',
        'harga',
        'type',
        'duration',
        'start_date',
    ];

    protected $casts = [
        'qty' => 'integer',
        'harga' => 'integer',
        'start_date' => 'date',
    ];

    public function sellerOrder()
    {
        return $this->belongsTo(SellerOrder::class);
    }

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
