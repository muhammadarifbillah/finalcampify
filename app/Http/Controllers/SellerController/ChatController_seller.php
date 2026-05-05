<?php

namespace App\Http\Controllers\SellerController;

use App\Http\Controllers\Controller;
use App\Models\SellerModels\Chat_seller;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController_seller extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $conversations = Chat_seller::where(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                      ->orWhere('receiver_id', $userId);
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($chat) use ($userId) {
                return $chat->sender_id == $userId
                    ? $chat->receiver_id
                    : $chat->sender_id;
            });

        return view('SellerView.chat.index_seller', compact('conversations'));
    }

    public function show($userId)
    {
        $currentUserId = auth()->id();

        $messages = Chat_seller::where(function ($query) use ($currentUserId, $userId) {
                $query->where(function ($q) use ($currentUserId, $userId) {
                    $q->where('sender_id', $currentUserId)
                      ->where('receiver_id', $userId);
                })->orWhere(function ($q) use ($currentUserId, $userId) {
                    $q->where('sender_id', $userId)
                      ->where('receiver_id', $currentUserId);
                });
            })
            ->orderBy('created_at', 'asc')
            ->get();

        Chat_seller::where('receiver_id', $currentUserId)
            ->where('sender_id', $userId)
            ->update(['is_read' => true]);

        $chatPartner = User::findOrFail($userId);

        return view('SellerView.chat.show_seller', compact('messages', 'chatPartner'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);

        Chat_seller::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'type' => 'text',
            'is_read' => false
        ]);

        return back();
    }
}