<?php
namespace App\Models\Pembeli;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order_pembeli extends Model
{
    use HasFactory;
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
    ];

public function details()
{
    return $this->hasMany(OrderDetail_pembeli::class, 'order_id');
}
}
