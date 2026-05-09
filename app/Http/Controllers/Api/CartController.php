<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pembeli\Keranjang_pembeli;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cart = Keranjang_pembeli::with('product')
            ->where('user_id', Auth::id())
            ->get();

        return response()->json([
            'success' => true,
            'data' => $cart
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:buy,rent',
            'quantity' => 'required_if:type,buy|integer|min:1',
            'duration' => 'required_if:type,rent|integer|min:1',
            'start_date' => 'required_if:type,rent|date|after_or_equal:today',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($request->type === 'buy') {
            $stock = $product->stock ?? $product->stok ?? 0;
            if ($request->quantity > $stock) {
                return response()->json(['message' => 'Stok tidak mencukupi'], 422);
            }
        }

        if ($request->type === 'rent') {
            $rentPrice = $product->rent_price ?? $product->harga ?? 0;
            if ($rentPrice <= 0) {
                return response()->json(['message' => 'Produk ini tidak tersedia untuk sewa'], 422);
            }
        }

        $cart = Keranjang_pembeli::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'qty' => $request->quantity ?? 1,
            'type' => $request->type,
            'duration' => $request->duration ?? null,
            'start_date' => $request->start_date ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk ditambahkan ke keranjang',
            'data' => $cart
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $cart = Keranjang_pembeli::where('user_id', Auth::id())->findOrFail($id);

        if ($request->has('quantity')) {
            $qty = max(1, $request->quantity);
            $product = $cart->product;
            $stock = $product->stock ?? $product->stok ?? 0;
            if ($cart->type === 'buy' && $qty > $stock) {
                return response()->json(['message' => 'Stok tidak mencukupi'], 422);
            }
            $cart->update(['qty' => $qty]);
        }
        
        if ($request->has('duration')) {
            $cart->update(['duration' => max(1, $request->duration)]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Keranjang diperbarui',
            'data' => $cart
        ]);
    }

    public function remove($id)
    {
        $cart = Keranjang_pembeli::where('user_id', Auth::id())->findOrFail($id);
        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item dihapus dari keranjang'
        ]);
    }
}
