<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'user_id',
        'sender',
        'sender_id',
        'receiver_id',
        'order_id',
        'message',
        'type',
        'is_read',
        'is_flagged'
    ];

    // relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function senderUser()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }
}
