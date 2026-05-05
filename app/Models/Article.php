<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';

    protected $casts = [
        'waktu_posting' => 'datetime',
        'views' => 'integer',
    ];

    protected $fillable = [
        'user_id',
        'title',
        'judul',
        'content',
        'konten',
        'image',
        'gambar',
        'kategori_slug',
        'status',
        'thumbnail',
        'waktu_posting',
        'views',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getTitleAttribute()
    {
        return $this->attributes['title'] ?? null;
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
    }

    public function getJudulAttribute()
    {
        return $this->attributes['title'] ?? null;
    }

    public function setJudulAttribute($value)
    {
        $this->attributes['title'] = $value;
    }

    public function getContentAttribute()
    {
        return $this->attributes['content'] ?? null;
    }

    public function setContentAttribute($value)
    {
        $this->attributes['content'] = $value;
    }

    public function getKontenAttribute()
    {
        return $this->attributes['content'] ?? null;
    }

    public function setKontenAttribute($value)
    {
        $this->attributes['content'] = $value;
    }

    public function getImageAttribute()
    {
        return $this->attributes['image'] ?? null;
    }

    public function setImageAttribute($value)
    {
        $this->attributes['image'] = $value;
    }

    public function getGambarAttribute()
    {
        return $this->attributes['image'] ?? null;
    }

    public function setGambarAttribute($value)
    {
        $this->attributes['image'] = $value;
    }
}
