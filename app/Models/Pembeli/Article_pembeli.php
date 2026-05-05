<?php

namespace App\Models\Pembeli;

use Illuminate\Database\Eloquent\Model;

class Article_pembeli extends Model
{
    protected $table = 'articles';

    protected $fillable = [
        'title',
        'excerpt',
        'content',
        'image',
        'category',
        'author',
        'date'
    ];
}

