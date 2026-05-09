<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $conversations = Conversation::with(['product', 'seller.store', 'latestMessage.sender'])
            ->where('buyer_id', Auth::id())
            ->latest('updated_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $conversations
        ]);
    }

    public function show($id)
    {
        $conversation = Conversation::with(['product', 'seller.store', 'messages.sender'])
            ->where('buyer_id', Auth::id())
            ->findOrFail($id);

        // Mark messages as read
        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'data' => $conversation
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'message' => 'required|string|max:1000',
        ]);

        $conversation = Conversation::findOrFail($request->conversation_id);
        
        if ($conversation->buyer_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);

        $conversation->touch();

        return response()->json([
            'success' => true,
            'data' => $message
        ], 201);
    }

    public function start(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($request->product_id);
        $sellerId = $product->sellerUserId();

        if (!$sellerId || $sellerId === Auth::id()) {
            return response()->json(['message' => 'Invalid seller'], 400);
        }

        $conversation = Conversation::firstOrCreate([
            'product_id' => $product->id,
            'buyer_id' => Auth::id(),
            'seller_id' => $sellerId,
        ]);

        return response()->json([
            'success' => true,
            'data' => $conversation
        ]);
    }
}
