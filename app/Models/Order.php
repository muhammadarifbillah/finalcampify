<?php

namespace App\Models;

use App\Services\SellerOrderService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
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
        'video_pengiriman',
        'video_pengiriman_hash',
        'latitude',
        'longitude',
        'is_disbursed',
        'disbursed_at',
        'received_at',
    ];

    protected $casts = [
        'total' => 'integer',
        'is_disbursed' => 'boolean',
        'disbursed_at' => 'datetime',
        'received_at' => 'datetime',
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

    public function disbursements()
    {
        return $this->hasMany(OrderDisbursement::class, 'order_id');
    }

    public function sellerOrders()
    {
        return $this->hasMany(SellerOrder::class, 'order_id');
    }

    protected static function booted(): void
    {
        static::saved(function (Order $order) {
            if ($order->wasChanged(['status', 'received_at', 'kurir', 'no_resi', 'video_pengiriman', 'video_pengiriman_hash'])) {
                app(SellerOrderService::class)->syncForOrder($order->fresh(['details.product.store']));
            }
        });
    }
}
