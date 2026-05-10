<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Pembeli\Product_pembeli;
use App\Models\Pembeli\Order_pembeli;
use App\Models\Pembeli\Rental_pembeli;
use App\Models\Pembeli\Wishlist_pembeli;

class PembeliProductController extends Controller
{
    // ================= FORM SEWA =================
    public function formSewa($id)
    {
        $produk = Product_pembeli::with('store')->findOrFail($id);
        $user = auth()->user();
        return view('pembeli.sewa.form_pembeli', compact('produk', 'user'));
    }

    // ================= PROSES SEWA =================
    public function processSewa(Request $request)
    {
        $requestData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'duration' => 'required|numeric|min:1',
            'alamat' => 'required|string',
            'kota' => 'required|string',
            'kecamatan' => 'required|string',
            'kode_pos' => 'required|string',
            'telepon' => 'required|string',
            'metode_pengiriman' => 'required|in:kurir,standar',
            'metode_pembayaran' => 'required|in:qris,va,cod',
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'ktp_image' => Auth::user()->ktp_image ? 'nullable|image|max:2048' : 'required|image|max:2048',
        ]);

        $user = auth()->user();
        
        // Update Profile User secara otomatis
        $user->update([
            'address' => $requestData['alamat'],
            'city' => $requestData['kota'],
            'district' => $requestData['kecamatan'],
            'postal_code' => $requestData['kode_pos'],
            'phone' => $requestData['telepon'],
        ]);

        $user = Auth::user();
        $produk = Product_pembeli::findOrFail($requestData['product_id']);
        $rentalFee = $produk->rent_price * $requestData['duration'];
        $deposit = $produk->buy_price * 0.5;
        $totalPrice = $rentalFee + $deposit;

        // Handle KTP Upload if provided (Mandatory check handled by Frontend 'required')
        if ($request->hasFile('ktp_image')) {
            $file = $request->file('ktp_image');
            $filename = time() . '_ktp_' . $user->id . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/ktp_uploads'), $filename);
            $user->update([
                'ktp_image' => 'storage/ktp_uploads/' . $filename,
                'ktp_verified_at' => null // Reset verification if new upload
            ]);
        }

        // 0. Handle Bukti Pembayaran
        $buktiPath = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/pembayaran'), $filename);
            $buktiPath = 'uploads/pembayaran/' . $filename;
        }

        // 1. Buat Header Order
        $pesanan = Order_pembeli::create([
            'user_id' => $user->id,
            'receiver_name' => $user->name,
            'total' => $totalPrice,
            'shipping_address' => $user->address,
            'shipping_city' => $user->city,
            'shipping_district' => $user->district,
            'shipping_postal_code' => $user->postal_code,
            'shipping_phone' => $user->phone,
            'metode_pembayaran' => $requestData['metode_pembayaran'],
            'status' => 'menunggu', 
            'kurir' => $requestData['metode_pengiriman'],
            'bukti_pembayaran' => $buktiPath,
        ]);

        // 2. Buat Detail Order
        \App\Models\Pembeli\OrderDetail_pembeli::create([
            'order_id' => $pesanan->id,
            'product_id' => $produk->id,
            'qty' => 1,
            'harga' => $rentalFee, // Simpan harga sewa saja di detail item (breakdown)
            'type' => 'rent',
            'duration' => $request->duration,
            'start_date' => $request->start_date,
        ]);
        
        // 3. Buat Record Rental (untuk Seller)
        Rental_pembeli::create([
            'user_id' => $user->id,
            'product_id' => $produk->id,
            'order_id' => $pesanan->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'duration' => $request->duration,
            'price' => $rentalFee, // Hanya biaya sewa
            'status' => 'pending' // Status awal di tabel rentals
        ]);

        return redirect()->route('orders.detail', $pesanan->id)->with('success', 'Pengajuan sewa berhasil dibuat!');
    }

    // ================= BELI =================
    public function index()
    {
        $produks = Product_pembeli::where('buy_price', '>', 0)
            ->where('jenis_produk', 'jual')
            ->latest()
            ->paginate(12);

        $wishlistProductIds = $this->getWishlistProductIds();

        return view('pembeli.produk.index_pembeli', [
            'produks' => $produks,
            'wishlistProductIds' => $wishlistProductIds,
            'mode' => 'buy'
        ]);
    }

    // ================= SEWA =================
    public function rentalProducts()
    {
        $produks = Product_pembeli::where('is_rental', true)
            ->latest()
            ->paginate(12);

        $wishlistProductIds = $this->getWishlistProductIds();

        return view('pembeli.produk.index_pembeli', [
            'produks' => $produks,
            'wishlistProductIds' => $wishlistProductIds,
            'mode' => 'rent'
        ]);
    }

    // ================= DETAIL REDIRECT =================
    public function detail($id)
    {
        $produk = Product_pembeli::findOrFail($id);

        // arahkan sesuai tipe
        if ($produk->buy_price > 0) {
            return redirect()->route('produk.detail.buy', $id);
        } elseif ($produk->rent_price > 0) {
            return redirect()->route('produk.detail.rent', $id);
        }

        abort(404);
    }

    // ================= DETAIL BELI =================
    public function detailBuy($id)
    {
        $produk = Product_pembeli::with('productRatings.user')->findOrFail($id);

        // kunci: harus produk beli
        if (!$produk->buy_price || $produk->buy_price <= 0) {
            abort(404);
        }

        $wishlistProductIds = $this->getWishlistProductIds();

        return view('pembeli.produk.detail-buy_pembeli', [
            'produk' => $produk,
            'wishlistProductIds' => $wishlistProductIds,
        ]);
    }

    // ================= DETAIL SEWA =================
    public function detailRent($id)
    {
        $produk = Product_pembeli::with('productRatings.user')->findOrFail($id);

        // kunci: harus produk sewa
        if (!$produk->rent_price || $produk->rent_price <= 0) {
            abort(404);
        }

        $wishlistProductIds = $this->getWishlistProductIds();

        return view('pembeli.produk.detail-rent_pembeli', [
            'produk' => $produk,
            'wishlistProductIds' => $wishlistProductIds,
        ]);
    }

    // ================= CHECKOUT =================
    public function checkoutProduk($id)
    {
        $produk = Product_pembeli::findOrFail($id);
        return view('pembeli.checkout.produk_pembeli', compact('produk'));
    }

    // ================= SEARCH =================
    public function search(Request $request)
    {
        $keyword = $request->q;
        $category = $request->category;
        $minPrice = $request->min_price;
        $maxPrice = $request->max_price;
        $sort = $request->sort ?? 'latest';

        $query = Product_pembeli::query();

        if ($keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%$keyword%")
                  ->orWhere('description', 'like', "%$keyword%");
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        if ($minPrice) {
            $query->where('buy_price', '>=', $minPrice);
        }

        if ($maxPrice) {
            $query->where('buy_price', '<=', $maxPrice);
        }

        switch ($sort) {
            case 'price_low':
                $query->orderBy('buy_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('buy_price', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            default:
                $query->latest();
        }

        $produks = $query->paginate(12);

        return view('pembeli.produk.index_pembeli', [
            'produks' => $produks,
            'mode' => 'buy'
        ]);
    }

    // ================= CATEGORY =================
    public function category($category)
    {
        $products = Product_pembeli::where('category', $category)->get();

        return view('pembeli.category.index_pembeli', [
            'category' => $category,
            'products' => $products
        ]);
    }

    // ================= WISHLIST HELPER =================
    protected function getWishlistProductIds()
    {
        if (!auth()->check()) {
            return [];
        }

        return Wishlist_pembeli::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->pluck('product_id')
            ->toArray();
    }
}