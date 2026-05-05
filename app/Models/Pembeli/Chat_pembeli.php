<?php

namespace App\Models\Pembeli;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pembeli\User_pembeli;

class Chat_pembeli extends Model
{
    use HasFactory;

    protected $table = 'chats';

    protected $fillable = [
        'user_id',
        'sender',
        'sender_id',
        'receiver_id',
        'order_id',
        'message',
        'type',
        'is_read',
        'is_flagged',
    ];

    public function user()
    {
        return $this->belongsTo(User_pembeli::class, 'user_id');
    }
}
