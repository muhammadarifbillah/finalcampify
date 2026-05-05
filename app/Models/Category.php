<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'nama_kategori',
        'slug',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'kategori_id');
    }
}
