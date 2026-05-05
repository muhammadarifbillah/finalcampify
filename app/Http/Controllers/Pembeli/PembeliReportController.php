<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Product;
use App\Models\Report;
use App\Models\Store;
use Illuminate\Http\Request;

class PembeliReportController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'reason' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        $sellerId = $product->sellerUserId();

        abort_unless($sellerId && $sellerId !== \Illuminate\Support\Facades\Auth::id(), 422, 'Produk tidak memiliki seller valid.');

        Report::create([
            'reporter_id' => \Illuminate\Support\Facades\Auth::id(),
            'seller_id' => $sellerId,
            'store_id' => $product->store_id,
            'product_id' => $product->id,
            'type' => 'product',
            'reason' => $request->reason,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Laporan dikirim ke admin.');
    }

    public function storeReport(Request $request, Store $store)
    {
        $request->validate([
            'reason' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        abort_unless($store->user_id && $store->user_id !== \Illuminate\Support\Facades\Auth::id(), 422, 'Toko tidak valid.');

        Report::create([
            'reporter_id' => \Illuminate\Support\Facades\Auth::id(),
            'seller_id' => $store->user_id,
            'store_id' => $store->id,
            'type' => 'store',
            'reason' => $request->reason,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Laporan toko dikirim ke admin.');
    }

    public function chat(Request $request, Conversation $conversation)
    {
        abort_unless($conversation->buyer_id === \Illuminate\Support\Facades\Auth::id(), 403);

        $request->validate([
            'message_id' => 'nullable|exists:messages,id',
            'reason' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        $messageId = $request->message_id;

        if ($messageId) {
            abort_unless(Message::whereKey($messageId)->where('conversation_id', $conversation->id)->exists(), 403);
        }

        Report::create([
            'reporter_id' => \Illuminate\Support\Facades\Auth::id(),
            'seller_id' => $conversation->seller_id,
            'store_id' => $conversation->product?->store_id,
            'product_id' => $conversation->product_id,
            'conversation_id' => $conversation->id,
            'message_id' => $messageId,
            'type' => 'chat',
            'reason' => $request->reason,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Laporan chat dikirim ke admin.');
    }
}
