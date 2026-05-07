<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Store;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nama',
        'email',
        'password',
        'role',
        'status',
        'last_login',
        'address',
        'city',
        'district',
        'postal_code',
        'phone',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
        'last_login' => 'datetime',
    ];

    public function getNameAttribute()
    {
        return $this->attributes['name'] ?? $this->attributes['nama'] ?? null;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['nama'] = $value;
    }

    // 🔥 RELASI KE TOKO
    public function store()
    {
        return $this->hasOne(Store::class);
    }

    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'user_id', 'id');
    }

    public function sentChats()
    {
        return $this->hasMany(Chat::class, 'sender_id', 'id');
    }

    public function receivedChats()
    {
        return $this->hasMany(Chat::class, 'receiver_id', 'id');
    }

    public function buyerConversations()
    {
        return $this->hasMany(Conversation::class, 'buyer_id');
    }

    public function sellerConversations()
    {
        return $this->hasMany(Conversation::class, 'seller_id');
    }
}
