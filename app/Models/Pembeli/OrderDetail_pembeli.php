<?php
namespace App\Models\Pembeli;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail_pembeli extends Model
{
    use HasFactory;
    protected $table = 'order_details';
    protected $fillable = ['order_id','product_id','qty','harga','type','duration','start_date'];
    protected $casts = [
        'start_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product_pembeli::class, 'product_id');
    }
}
