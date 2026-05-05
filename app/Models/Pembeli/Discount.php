<?php

namespace App\Models\Pembeli;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $table = 'discounts';

    protected $fillable = [
        'kode',
        'persen',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];
}
