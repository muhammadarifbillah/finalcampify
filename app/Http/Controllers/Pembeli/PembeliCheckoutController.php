<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Pembeli\Keranjang_pembeli;
use App\Models\Pembeli\Product_pembeli;
use App\Models\Pembeli\Order_pembeli;
use App\Models\Pembeli\OrderDetail_pembeli;
use Illuminate\Support\Facades\Auth;

class PembeliCheckoutController extends Controller
{
    public function index()
    {
        $cart = Keranjang_pembeli::with('product')
            ->where('user_id', Auth::id())
            ->get();

        if ($cart->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Keranjang masih kosong.');
        }

        return view('pembeli.checkout.index_pembeli', compact('cart'));
    }

    public function produk($id)
    {
        $produk = Product_pembeli::findOrFail($id);
        return view('pembeli.checkout.produk_pembeli', compact('produk'));
    }

    public function process(Request $r)
    {
        $cart = Keranjang_pembeli::with('product')
            ->where('user_id', Auth::id())
            ->get();

        if ($cart->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Keranjang kosong.');
        }

        $user = Auth::user();

        $requestData = $r->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:2000',
            'kota' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kode_pos' => 'required|string|max:20',
            'telepon' => 'required|string|max:25',
            'metode_pembayaran' => 'required|string|in:transfer,cod,ewallet',
            'shipping_method' => 'required|string|in:jne,gosend',
        ]);

        $user->update([
            'name' => $requestData['nama'],
            'address' => $requestData['alamat'],
            'city' => $requestData['kota'],
            'district' => $requestData['kecamatan'],
            'postal_code' => $requestData['kode_pos'],
            'phone' => $requestData['telepon'],
        ]);

        $subtotal = $cart->sum(function ($item) {
            $price = $item->type === 'buy' 
                ? $item->product->buy_price 
                : $item->product->rent_price * $item->duration;
            return $price * $item->qty;
        });

        $shippingRates = [
            'jne' => 15000,
            'gosend' => 25000,
        ];

        $shippingCost = $shippingRates[$requestData['shipping_method']] ?? 0;
        $total = $subtotal + $shippingCost;

        $pesanan = Order_pembeli::create([
            'user_id' => Auth::id(),
            'receiver_name' => $user->name,
            'total' => $total,
            'shipping_address' => $user->address,
            'shipping_city' => $user->city,
            'shipping_district' => $user->district,
            'shipping_postal_code' => $user->postal_code,
            'shipping_phone' => $user->phone,
            'metode_pembayaran' => $requestData['metode_pembayaran'],
            'kurir' => $requestData['shipping_method'],
            'status' => 'diproses',
        ]);

        foreach ($cart as $item) {
            $price = $item->type === 'buy' 
                ? $item->product->buy_price 
                : $item->product->rent_price * $item->duration;
                
            OrderDetail_pembeli::create([
                'order_id' => $pesanan->id,
                'product_id' => $item->product_id,
                'qty' => $item->qty,
                'harga' => $price,
                'type' => $item->type,
                'duration' => $item->duration,
                'start_date' => $item->start_date,
            ]);
        }

        Keranjang_pembeli::where('user_id', Auth::id())->delete();

        return redirect()
            ->route('orders.detail', $pesanan->id)
            ->with('success', 'Pesanan berhasil dibuat dan diproses.');
    }
}
