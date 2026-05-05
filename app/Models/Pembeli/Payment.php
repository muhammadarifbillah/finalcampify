<?php

namespace App\Models\Pembeli;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'order_id',
        'metode',
        'status',
        'tanggal',
    ];

    public function order()
    {
        return $this->belongsTo(Order_pembeli::class, 'order_id');
    }
}
