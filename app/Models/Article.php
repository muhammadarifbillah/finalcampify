<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';

    protected $fillable = [
        'user_id',
        'judul',
        'konten',
        'gambar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getTitleAttribute()
    {
        return $this->attributes['judul'] ?? null;
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['judul'] = $value;
    }

    public function getContentAttribute()
    {
        return $this->attributes['konten'] ?? null;
    }

    public function setContentAttribute($value)
    {
        $this->attributes['konten'] = $value;
    }

    public function getImageAttribute()
    {
        return $this->attributes['gambar'] ?? null;
    }

    public function setImageAttribute($value)
    {
        $this->attributes['gambar'] = $value;
    }
}