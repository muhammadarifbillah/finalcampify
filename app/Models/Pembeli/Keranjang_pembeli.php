<?php
namespace App\Models\Pembeli;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keranjang_pembeli extends Model
{
    use HasFactory;
    protected $table = 'keranjang';
    protected $fillable = ['user_id','product_id','qty','type','duration','start_date'];
    protected $casts = [
        'start_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product_pembeli::class, 'product_id');
    }
}
