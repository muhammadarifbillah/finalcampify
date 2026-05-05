<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Pembeli\Chat_pembeli;
use App\Models\Pembeli\StoreProfile_pembeli;

class PembeliChatController extends Controller
{
    public function index()
    {
        $messages = auth()->user()->chats()->orderBy('created_at')->get();
        $lastMessage = $messages->last();
        $store = StoreProfile_pembeli::first();
        $storeName = $store ? $store->store_name : 'Campify Admin';

        $contacts = [
            [
                'name' => $storeName,
                'online' => true,
                'last_message' => $lastMessage ? ($lastMessage->sender === 'user' ? 'Kamu: '.$lastMessage->message : $lastMessage->message) : 'Halo! Ada yang bisa kami bantu?',
                'time' => $lastMessage ? $lastMessage->created_at->format('H:i') : now()->format('H:i'),
            ],
        ];

        return view('pembeli.chat.index_pembeli', compact('messages', 'contacts'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        Chat_pembeli::create([
            'user_id' => auth()->id(),
            'sender' => 'user',
            'message' => $request->message,
        ]);

        return back();
    }
}