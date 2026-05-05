<?php

namespace App\Models\SellerModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\User;

class Chat_seller extends Model
{
    use HasFactory;

    protected $table = 'chats';

    protected $fillable = [
        'sender_id', 'receiver_id', 'order_id', 'message', 'type', 'is_read'
    ];

    public function sender() {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver() {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function order() {
        return $this->belongsTo(Order::class);
    }
}
