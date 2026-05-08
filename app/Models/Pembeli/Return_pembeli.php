<?php

namespace App\Models\Pembeli;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Return_pembeli extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $fillable = [
        'rental_id',
        'resi_return',
        'foto_kondisi',
        'bukti_denda',
        'kondisi_barang',
        'denda',
        'tanggal_pengembalian',
    ];

    protected $casts = [
        'denda' => 'integer',
        'tanggal_pengembalian' => 'datetime',
    ];

    public function rental()
    {
        return $this->belongsTo(Rental_pembeli::class, 'rental_id');
    }
}
