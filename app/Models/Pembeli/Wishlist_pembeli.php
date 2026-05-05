<?php

namespace App\Models\Pembeli;

use Illuminate\Database\Eloquent\Model;

class Wishlist_pembeli extends Model
{
    protected $table = 'wishlists';

    protected $fillable = ['user_id', 'product_id'];

    public function product()
    {
        return $this->belongsTo(Product_pembeli::class, 'product_id');
    }
}
