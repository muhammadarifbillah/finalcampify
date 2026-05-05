<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Pembeli\Product_pembeli;
use App\Models\Pembeli\Order_pembeli;
use App\Models\Pembeli\Wishlist_pembeli;

class PembeliProductController extends Controller
{
    // ================= FORM SEWA =================
    public function formSewa($id)
    {
        $produk = Product_pembeli::findOrFail($id);
        $user = auth()->user();
        return view('pembeli.sewa.form_pembeli', compact('produk', 'user'));
    }

    // ================= PROSES SEWA =================
    public function processSewa(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'duration' => 'required|integer|min:1',
            'alamat' => 'required|string',
            'metode_pembayaran' => 'required|string',
            'metode_pengiriman' => 'required|string',
        ]);

        $user = auth()->user();
        $produk = Product_pembeli::findOrFail($request->product_id);
        $totalPrice = $produk->rent_price * $request->duration;

        // 1. Buat Header Order
        $pesanan = Order_pembeli::create([
            'user_id' => $user->id,
            'receiver_name' => $user->name,
            'total' => $totalPrice,
            'shipping_address' => $request->alamat,
            'shipping_city' => $user->city ?? '-',
            'shipping_district' => $user->district ?? '-',
            'shipping_postal_code' => $user->postal_code ?? '-',
            'shipping_phone' => $user->phone ?? '-',
            'metode_pembayaran' => $request->metode_pembayaran,
            'status' => 'menunggu', // Gunakan 'menunggu' agar sesuai dengan filter di detail
            'kurir' => $request->metode_pengiriman,
        ]);

        // 2. Buat Detail Order
        \App\Models\Pembeli\OrderDetail_pembeli::create([
            'order_id' => $pesanan->id,
            'product_id' => $produk->id,
            'qty' => 1,
            'harga' => $totalPrice,
            'type' => 'rent',
            'duration' => $request->duration,
            'start_date' => $request->start_date,
        ]);

        return redirect()->route('orders.detail', $pesanan->id)->with('success', 'Pengajuan sewa berhasil dibuat!');
    }

    // ================= BELI =================
    public function index()
    {
        $produks = Product_pembeli::where('buy_price', '>', 0)
            ->where(function ($q) {
                $q->whereNull('rent_price')
                  ->orWhere('rent_price', 0);
            })
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
        $produks = Product_pembeli::where('rent_price', '>', 0)
            ->where(function ($q) {
                $q->whereNull('buy_price')
                  ->orWhere('buy_price', 0);
            })
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