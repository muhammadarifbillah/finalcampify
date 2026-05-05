<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Pembeli\Chat_pembeli;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Product;
use App\Models\User;

class PembeliChatController extends Controller
{
    public function index()
    {
        $userId = \Illuminate\Support\Facades\Auth::id();

        $conversations = Conversation::with(['product', 'seller.store', 'latestMessage.sender'])
            ->where('buyer_id', $userId)
            ->latest('updated_at')
            ->get();

        $conversation = $conversations->first();
        $messages = $conversation
            ? $conversation->messages()->with('sender')->orderBy('created_at')->get()
            : collect();

        return view('pembeli.chat.index_pembeli', compact('conversations', 'conversation', 'messages'));
    }

    public function start(Product $product)
    {
        $sellerId = $product->sellerUserId();

        if (!$sellerId || $sellerId === \Illuminate\Support\Facades\Auth::id()) {
            return back()->with('error', 'Penjual produk tidak valid.');
        }

        $conversation = Conversation::firstOrCreate([
            'product_id' => $product->id,
            'buyer_id' => \Illuminate\Support\Facades\Auth::id(),
            'seller_id' => $sellerId,
        ]);

        return redirect()->route('chat.show', $conversation);
    }

    public function show(Conversation $conversation)
    {
        abort_unless($conversation->buyer_id === \Illuminate\Support\Facades\Auth::id(), 403);

        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', \Illuminate\Support\Facades\Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $userId = \Illuminate\Support\Facades\Auth::id();
        $conversations = Conversation::with(['product', 'seller.store', 'latestMessage.sender'])
            ->where('buyer_id', $userId)
            ->latest('updated_at')
            ->get();

        $conversation->load(['product', 'seller.store']);
        $messages = $conversation->messages()->with('sender')->orderBy('created_at')->get();

        return view('pembeli.chat.index_pembeli', compact('conversations', 'conversation', 'messages'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        if ($request->filled('conversation_id')) {
            $conversation = Conversation::findOrFail($request->conversation_id);
            abort_unless($conversation->buyer_id === \Illuminate\Support\Facades\Auth::id(), 403);

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => \Illuminate\Support\Facades\Auth::id(),
                'message' => $request->message,
            ]);

            $conversation->touch();

            return redirect()->route('chat.show', $conversation);
        }

        Chat_pembeli::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'sender' => 'user',
            'sender_id' => \Illuminate\Support\Facades\Auth::id(),
            'receiver_id' => $this->defaultReceiverId(),
            'message' => $request->message,
            'type' => 'text',
            'is_read' => false,
        ]);

        return back();
    }

    public function store(Request $request, Conversation $conversation)
    {
        abort_unless($conversation->buyer_id === \Illuminate\Support\Facades\Auth::id(), 403);

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => \Illuminate\Support\Facades\Auth::id(),
            'message' => $request->message,
        ]);

        $conversation->touch();

        return redirect()->route('chat.show', $conversation);
    }

    private function defaultReceiverId(): ?int
    {
        return User::where('role', 'seller')->value('id')
            ?? User::where('role', 'admin')->value('id');
    }
}
