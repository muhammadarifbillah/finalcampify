<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Pembeli\Keranjang_pembeli;
use App\Models\Pembeli\Product_pembeli;
use Illuminate\Support\Facades\Auth;

class PembeliCartController extends Controller
{
    public function index()
    {
        $cart = Keranjang_pembeli::with('product')
            ->where('user_id', Auth::id())
            ->get();

        return view('pembeli.cart.index_pembeli', compact('cart'));
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

        $produk = Product_pembeli::findOrFail($request->product_id);

        if ($request->type === 'buy') {
            if ($request->quantity > $produk->stock) {
                return back()->with('error', 'Stok tidak mencukupi');
            }
        }

        if ($request->type === 'rent') {
            if (empty($produk->rent_price) || $produk->rent_price <= 0) {
                return back()->with('error', 'Produk ini tidak tersedia untuk sewa');
            }
        }

        Keranjang_pembeli::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'qty' => $request->quantity ?? 1,
            'type' => $request->type,
            'duration' => $request->duration ?? null,
            'start_date' => $request->start_date ?? null,
        ]);

        // Jika dari halaman checkout, redirect ke cart
        if ($request->has('redirect') && $request->redirect === 'checkout') {
            return redirect()->route('cart.index')->with('success', 'Produk ditambahkan ke keranjang. Silakan lanjutkan checkout');
        }

        return back()->with('success', 'Produk ditambahkan ke keranjang');
    }

    public function update(Request $request, $id)
    {
        $cart = Keranjang_pembeli::where('user_id', Auth::id())->findOrFail($id);

        if ($request->has('quantity')) {
            $qty = max(1, $request->quantity);
            if ($cart->type === 'buy' && $qty > $cart->product->stock) {
                return back()->with('error', 'Stok tidak mencukupi');
            }
            $cart->update(['qty' => $qty]);
        }
        
        if ($request->has('duration')) {
            $cart->update(['duration' => max(1, $request->duration)]);
        }

        return back()->with('success', 'Keranjang diperbarui');
    }

    public function remove($id)
    {
        $cart = Keranjang_pembeli::where('user_id', Auth::id())->findOrFail($id);
        $cart->delete();

        return back()->with('success', 'Item dihapus dari keranjang');
    }
}