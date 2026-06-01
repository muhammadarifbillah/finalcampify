<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDisbursement extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'store_id',
        'seller_id',
        'admin_id',
        'amount',
        'status',
        'source',
        'reference',
        'notes',
        'disbursed_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'disbursed_at' => 'datetime',
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

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
