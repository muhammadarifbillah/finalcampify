<?php

namespace App\Models\Pembeli;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Return_pembeli extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $fillable = [
        'order_id',
        'rental_id',
        'type',
        'status',
        'escrow_total',
        'expected_date',
        'actual_date',
        'late_fee',
        'damage_fee',
        'to_seller',
        'to_buyer',
        'deposit_amount',
        'rental_fee_amount',
        'resi_return',
        'foto_kondisi',
        'bukti_denda',
        'kondisi_barang',
        'denda',
        'tanggal_pengembalian',
    ];

    protected $casts = [
        'escrow_total' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'damage_fee' => 'decimal:2',
        'to_seller' => 'decimal:2',
        'to_buyer' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'rental_fee_amount' => 'decimal:2',
        'expected_date' => 'datetime',
        'actual_date' => 'datetime',
        'tanggal_pengembalian' => 'datetime',
    ];
}
