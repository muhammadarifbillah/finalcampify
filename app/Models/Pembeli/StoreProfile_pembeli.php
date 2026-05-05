<?php

namespace App\Models\Pembeli;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreProfile_pembeli extends Model
{
    protected $table = 'store_profiles';

    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_name',
        'description',
        'address',
        'image',
    ];

    public function user()
    {
        return $this->belongsTo(User_pembeli::class, 'user_id');
    }
}
