<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Product;
use App\Models\Report;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function reportProduct(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $request->validate([
            'reason' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        $sellerId = $product->sellerUserId();

        if (!$sellerId || $sellerId === Auth::id()) {
            return response()->json(['message' => 'Invalid product or seller'], 422);
        }

        $report = Report::create([
            'reporter_id' => Auth::id(),
            'seller_id' => $sellerId,
            'store_id' => $product->store_id,
            'product_id' => $product->id,
            'type' => 'product',
            'reason' => $request->reason,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Laporan produk berhasil dikirim.',
            'data' => $report
        ], 201);
    }

    public function reportStore(Request $request, $storeId)
    {
        $store = Store::findOrFail($storeId);
        $request->validate([
            'reason' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        if (!$store->user_id || $store->user_id === Auth::id()) {
            return response()->json(['message' => 'Invalid store'], 422);
        }

        $report = Report::create([
            'reporter_id' => Auth::id(),
            'seller_id' => $store->user_id,
            'store_id' => $store->id,
            'type' => 'store',
            'reason' => $request->reason,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Laporan toko berhasil dikirim.',
            'data' => $report
        ], 201);
    }

    public function reportChat(Request $request, $conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        
        if ($conversation->buyer_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'message_id' => 'nullable|exists:messages,id',
            'reason' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        $messageId = $request->message_id;

        if ($messageId) {
            $messageExists = Message::whereKey($messageId)
                ->where('conversation_id', $conversation->id)
                ->exists();
            if (!$messageExists) {
                return response()->json(['message' => 'Invalid message ID'], 403);
            }
        }

        $report = Report::create([
            'reporter_id' => Auth::id(),
            'seller_id' => $conversation->seller_id,
            'store_id' => $conversation->product?->store_id,
            'product_id' => $conversation->product_id,
            'conversation_id' => $conversation->id,
            'message_id' => $messageId,
            'type' => 'chat',
            'reason' => $request->reason,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Laporan chat berhasil dikirim.',
            'data' => $report
        ], 201);
    }
}
