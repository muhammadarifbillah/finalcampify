<?php

namespace App\Models\Pembeli;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRating_pembeli extends Model
{
    protected $table = 'product_ratings';

    use HasFactory;

    protected $fillable = ['user_id', 'product_id', 'order_id', 'rating', 'comment', 'ulasan'];

    public function setCommentAttribute($value): void
    {
        $this->attributes['comment'] = $value;
        $this->attributes['ulasan'] = $value;
    }

    public function user()
    {
        return $this->belongsTo(User_pembeli::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product_pembeli::class, 'product_id');
    }

    public function order()
    {
        return $this->belongsTo(Order_pembeli::class, 'order_id');
    }
}
