<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    use HasFactory;

    public const STATUS_WAITING_DELIVERY = 'WAITING_DELIVERY';
    public const STATUS_WAITING_HOLD = 'WAITING_HOLD';
    public const STATUS_READY_TO_DISBURSE = 'READY_TO_DISBURSE';
    public const STATUS_DISBURSED = 'DISBURSED';

    protected $fillable = [
        'seller_order_id',
        'amount',
        'status',
        'ready_at',
        'disbursed_at',
        'source',
    ];

    protected $casts = [
        'amount' => 'integer',
        'ready_at' => 'datetime',
        'disbursed_at' => 'datetime',
    ];

    public function sellerOrder()
    {
        return $this->belongsTo(SellerOrder::class);
    }
}
