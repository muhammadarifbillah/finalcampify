<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerOrder extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'seller_order_number',
        'order_id',
        'store_id',
        'seller_id',
        'subtotal',
        'status',
        'kurir',
        'no_resi',
        'video_pengiriman',
        'video_pengiriman_hash',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    protected $appends = [
        'buyer',
        'buyer_name',
        'qty',
        'resi',
        'total',
        'status_label',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function items()
    {
        return $this->hasMany(SellerOrderItem::class);
    }

    public function details()
    {
        return $this->items();
    }

    public function payout()
    {
        return $this->hasOne(Payout::class);
    }

    public function getBuyerAttribute()
    {
        return $this->order?->buyer;
    }

    public function getBuyerNameAttribute(): ?string
    {
        return $this->buyer?->name ?? $this->order?->receiver_name;
    }

    public function getQtyAttribute(): int
    {
        return (int) $this->items->sum('qty');
    }

    public function getResiAttribute(): ?string
    {
        return $this->no_resi;
    }

    public function setResiAttribute($value): void
    {
        $this->attributes['no_resi'] = $value;
    }

    public function getTotalAttribute(): int
    {
        return (int) $this->subtotal;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu',
            self::STATUS_PROCESSING => 'Diproses',
            self::STATUS_SHIPPED => 'Dikirim',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => ucfirst((string) $this->status),
        };
    }

    public function getReceiverNameAttribute(): ?string
    {
        return $this->order?->receiver_name;
    }

    public function getShippingAddressAttribute(): ?string
    {
        return $this->order?->shipping_address;
    }

    public function getShippingCityAttribute(): ?string
    {
        return $this->order?->shipping_city;
    }

    public function getShippingDistrictAttribute(): ?string
    {
        return $this->order?->shipping_district;
    }

    public function getShippingPostalCodeAttribute(): ?string
    {
        return $this->order?->shipping_postal_code;
    }

    public function getShippingPhoneAttribute(): ?string
    {
        return $this->order?->shipping_phone;
    }
}
