<?php
namespace App\Models\Pembeli;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Order;

class Order_pembeli extends Order
{
    protected $table = 'orders';
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
        'bukti_pembayaran',
        'latitude',
        'longitude',
    ];

public function details()
{
    return $this->hasMany(OrderDetail_pembeli::class, 'order_id', 'id');
}
}
