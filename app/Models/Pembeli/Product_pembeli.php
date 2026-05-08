<?php
namespace App\Models\Pembeli;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Store;
use App\Models\User;

class Product_pembeli extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = ['name','category','description','buy_price','rent_price','rating','reviews_count','image','stock'];

    public function productRatings()
    {
        return $this->hasMany(ProductRating_pembeli::class, 'product_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'user_id', 'user_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sellerUserId(): ?int
    {
        return $this->seller_id
            ?? $this->user_id
            ?? $this->store?->user_id;
    }
}
