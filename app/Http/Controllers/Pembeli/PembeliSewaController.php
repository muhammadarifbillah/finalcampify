<?php
namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Pembeli\Product_pembeli;
use App\Models\Pembeli\Order_pembeli;
use Illuminate\Support\Facades\Auth;

class PembeliSewaController extends Controller
{
    // Form sewa
    public function form($id)
    {
        $produk = Product_pembeli::findOrFail($id);
        return view('pembeli.sewa.form_pembeli', compact('produk'));
    }

    // Proses sewa
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);
        $produk = Product_pembeli::findOrFail($request->product_id);
        $start = \Carbon\Carbon::parse($request->tanggal_mulai);
        $end = \Carbon\Carbon::parse($request->tanggal_selesai);
        $duration = max(1, $start->diffInDays($end) + 1);
        $price = (int) ($produk->rent_price ?: $produk->buy_price ?: 0) * $duration;

        $pesanan = Order::create([
            'user_id' => Auth::id(),
            'receiver_name' => Auth::user()->name,
            'total' => $price,
            'shipping_address' => Auth::user()->address,
            'shipping_city' => Auth::user()->city,
            'shipping_district' => Auth::user()->district,
            'shipping_postal_code' => Auth::user()->postal_code,
            'shipping_phone' => Auth::user()->phone,
            'metode_pembayaran' => 'transfer',
            'status' => 'menunggu',
        ]);

        OrderDetail::create([
            'order_id' => $pesanan->id,
            'product_id' => $produk->id,
            'qty' => 1,
            'harga' => $price,
            'type' => 'rent',
            'duration' => $duration,
            'start_date' => $start->toDateString(),
        ]);

        return redirect()->route('orders.detail', $pesanan->id)->with('success', 'Sewa berhasil dibuat!');
    }

    // Form pengembalian
    public function pengembalianForm($id)
    {
        $pesanan = Order_pembeli::findOrFail($id);
        return view('pembeli.sewa.pengembalian', compact('pesanan'));
    }

    // Proses pengembalian
    public function pengembalianStore(Request $request, $id)
    {
        $pesanan = Order_pembeli::findOrFail($id);
        $pesanan->status = 'dikembalikan';
        // Hitung denda jika telat atau hilang
        $now = now();
        if ($now->gt($pesanan->tanggal_selesai)) {
            $selisih = $now->diffInDays($pesanan->tanggal_selesai);
            $pesanan->denda = $selisih * 10000; // contoh denda per hari
        }
        if ($request->barang_hilang) {
            $pesanan->denda += 50000; // contoh denda barang hilang
        }
        $pesanan->save();
        return redirect()->route('orders.index')->with('success', 'Pengembalian berhasil!');
    }
}
