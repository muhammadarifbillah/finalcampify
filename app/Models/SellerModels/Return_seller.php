<?php
namespace App\Models\SellerModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Return_seller extends Model
{
    use HasFactory;
    protected $table = 'returns';
    protected $fillable = [
        'rental_id', 
        'resi_return', 
        'proof_returned_image', 
        'proof_sent_image', 
        'bukti_denda', 
        'kondisi_barang', 
        'denda', 
        'tanggal_pengembalian',
        'buyer_refund_bank_name',
        'buyer_refund_bank_account',
        'buyer_refund_bank_name_owner',
        'refund_disbursed_at',
    ];
    protected $casts = [
        'tanggal_pengembalian' => 'datetime',
        'refund_disbursed_at' => 'datetime',
        'denda' => 'integer'
    ];

    public function rental()
    {
        return $this->belongsTo(Rental_seller::class, 'rental_id');
    }
}
