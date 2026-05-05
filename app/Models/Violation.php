<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'admin_id',
        'report_id',
        'product_id',
        'source',
        'action',
        'strike_count',
        'reason',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
