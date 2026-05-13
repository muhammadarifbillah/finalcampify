<?php
namespace App\Models\Pembeli;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Order;
use App\Models\User;

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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details()
    {
        return $this->hasMany(OrderDetail_pembeli::class, 'order_id', 'id');
    }

    public function returnRequest()
    {
        return $this->hasOne(\App\Models\ReturnEscrow::class, 'order_id');
    }
}
