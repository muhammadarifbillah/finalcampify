<?php
namespace App\Models\Pembeli;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_pembeli extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = ['name','category','description','buy_price','rent_price','rating','reviews_count','image','stock'];

    public function productRatings()
    {
        return $this->hasMany(ProductRating_pembeli::class, 'product_id');
    }
}
