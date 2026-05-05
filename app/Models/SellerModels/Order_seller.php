<?php

namespace App\Models\SellerModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SellerModels\Product_seller;
use App\Models\User;

class Order_seller extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [

        'buyer_id', 'product_id', 'qty', 'status', 'resi'
    ];

    public function buyer() {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function product() {
        return $this->belongsTo(Product_seller::class);
    }
}
