<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Models\Courier;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['couriers', 'store'])->where('status', 'pending')->get();
        $couriers = Courier::all();
        $stores = Store::all();

        return view('admin.products', compact('products', 'couriers', 'stores'));
    }

    // Halaman list semua produk (bukan hanya pending)
    public function list()
    {
        $products = Product::with(['store', 'couriers'])->latest()->get();
        return view('admin.product_list', compact('products'));
    }

    // Halaman detail produk
    public function show($id)
    {
        $product = Product::with(['store', 'couriers'])->findOrFail($id);

        // ✅ VERIFIKASI: Produk harus dikelola oleh toko, user hanya bisa lihat produk toko mereka
        if (auth()->check() && auth()->user()->role !== 'admin' && $product->store->user_id !== auth()->user()->id) {
            return back()->with('error', 'Anda tidak memiliki akses ke produk ini.');
        }

        $stats = [
            'rating' => $product->rating,
            'reviews' => $product->reviews_count,
            'stock' => $product->stock,
            'price_buy' => $product->buy_price,
            'price_rent' => $product->rent_price ?? 0,
        ];

        return view('admin.product_detail', compact('product', 'stats'));
    }

    public function store(StoreProductRequest $request)
    {
        // ✅ VERIFIKASI: StoreProductRequest sudah menghandle validasi dan otorisasi
        $data = $request->validated();

        // ✅ VERIFIKASI: Cek produk benar-benar dikelola oleh toko yang dipilih
        $store = Store::findOrFail($data['store_id']);

        if (!$store) {
            return back()->withErrors('Toko tidak ditemukan.');
        }

        $product = Product::create([
            'name' => $data['name'],
            'category' => $data['category'] ?? 'Umum',
            'description' => $data['description'] ?? '-',
            'buy_price' => $data['buy_price'],
            'rent_price' => $data['rent_price'] ?? 0,
            'price' => $data['buy_price'],
            'stock' => $data['stock'],
            'image' => $data['image'] ?? 'https://via.placeholder.com/600x400?text=No+Image',
            'status' => 'pending',
            'is_rental' => !empty($data['rent_price']),
            'rating' => 0,
            'reviews_count' => 0,
            'store_id' => $data['store_id'], // ✅ Harus sesuai dengan toko
        ]);

        if (!empty($data['couriers'])) {
            $product->couriers()->sync($data['couriers']);
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke toko ' . $store->nama_toko . '.');
    }

    public function approve($id)
    {
        Product::findOrFail($id)->update(['status' => 'approved']);
        return back();
    }

    public function reject($id)
    {
        Product::findOrFail($id)->update(['status' => 'rejected']);
        return back();
    }
}