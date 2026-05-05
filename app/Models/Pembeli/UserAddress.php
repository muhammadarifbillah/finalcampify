<?php

namespace App\Models\Pembeli;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $table = 'user_addresses';

    protected $fillable = [
        'user_id',
        'alamat',
        'kota',
        'kode_pos',
    ];

    public function user()
    {
        return $this->belongsTo(User_pembeli::class, 'user_id');
    }
}
