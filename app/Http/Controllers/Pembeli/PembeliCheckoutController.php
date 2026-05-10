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
        $cart = Keranjang_pembeli::with('product.store')
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

        $couriers = \App\Models\Courier::where('status', 'aktif')->get();
        $validServices = $couriers->pluck('service')->toArray();

        $requestData = $r->validate([
            'nama'              => 'required|string|max:255',
            'alamat'            => 'required|string|max:2000',
            'kota'              => 'required|string|max:255',
            'kecamatan'        => 'required|string|max:255',
            'kode_pos'         => 'required|string|max:20',
            'telepon'          => 'required|string|max:25',
            'metode_pembayaran' => 'required|string|in:transfer,cod',
            'shipping_method'  => ['required', 'string', \Illuminate\Validation\Rule::in($validServices)],
            'bukti_pembayaran' => 'required_if:metode_pembayaran,transfer|nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
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

        // Ambil ongkir dari database berdasarkan kurir yang dipilih
        $selectedCourier = $couriers->firstWhere('service', $requestData['shipping_method']);
        $shippingCost = $selectedCourier ? $selectedCourier->price : 0;
        $total = $subtotal + $shippingCost;

        $buktiPath = null;
        if ($r->hasFile('bukti_pembayaran')) {
            $file = $r->file('bukti_pembayaran');
            $filename = time() . '_' . Auth::id() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/pembayaran'), $filename);
            $buktiPath = 'uploads/pembayaran/' . $filename;
        }

        $pesananData = [
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
            'bukti_pembayaran' => $buktiPath,
        ];

        // Otomatis Geocoding Alamat Pengiriman
        try {
            $fullAddress = $user->address . ', ' . $user->district . ', ' . $user->city;
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->withHeaders([
                'User-Agent' => 'CampifyApp/1.0'
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $fullAddress,
                'format' => 'json',
                'limit' => 1
            ]);

            if ($response->successful() && isset($response->json()[0])) {
                $geo = $response->json()[0];
                $pesananData['latitude'] = $geo['lat'];
                $pesananData['longitude'] = $geo['lon'];
            }
        } catch (\Exception $e) {
            // fail silently
        }

        $pesanan = Order_pembeli::create($pesananData);

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
