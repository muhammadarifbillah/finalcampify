<?php

namespace App\Http\Controllers\SellerController;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use App\Models\SellerModels\Product_seller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController_seller extends Controller
{
    /**
     * Tampilkan semua produk seller login
     * Kelola Produk = semua produk
     */
    public function index(Request $request)
    {
        $query = Product_seller::where('user_id', Auth::id());

        // filter jenis
        if ($request->filled('jenis')) {
            $query->where('jenis_produk', $request->jenis);
        }

        $products = $query->latest()->get();

        return view('SellerView.products.index_seller', compact('products'));
    }

    /**
     * Form tambah produk
     */
    public function create()
    {
        return view('SellerView.products.create_seller');
    }

    /**
     * Simpan produk baru ke database
     * Bisa masuk ke:
     * - Kelola Produk
     * - Dashboard
     * - Penyewaan (jika jenis_produk = sewa)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_produk'   => 'required|string|max:255',
            'harga'         => 'required|numeric',
            'kategori'      => 'required|string|max:255',
            'jenis_produk'  => 'required|in:jual,sewa',
            'stok'          => 'required|integer|min:0',
            'deskripsi'     => 'nullable|string',
            'gambar'         => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // Upload gambar
        $imagePath = null;
        if ($request->hasFile('gambar')) {
            $imagePath = $request->file('gambar')->store('products', 'public');
        }

        $flagReasons = \App\Models\Product::flagReasonsFor($request->only(['nama_produk', 'harga', 'deskripsi']));
        $store = Store::where('user_id', Auth::id())->first();

        // Simpan ke database
        Product_seller::create([
            'user_id'       => \Illuminate\Support\Facades\Auth::id(),
            'store_id'      => $store?->id,
            'name'          => $request->nama_produk,
            'nama_produk'   => $request->nama_produk,
            'description'   => $request->deskripsi,
            'deskripsi'     => $request->deskripsi,
            'price'         => $request->harga,
            'harga'         => $request->harga,
            'buy_price'     => $request->jenis_produk === 'jual' ? $request->harga : 0,
            'rent_price'    => $request->jenis_produk === 'sewa' ? $request->harga : 0,
            'category'      => $request->kategori,
            'kategori'      => $request->kategori,
            'jenis_produk'  => $request->jenis_produk, // jual / sewa
            'is_rental'     => $request->jenis_produk === 'sewa',
            'stock'         => $request->stok,
            'stok'          => $request->stok,
            'image'         => $imagePath,
            'gambar'        => $imagePath,
            'status'        => 'waiting',
            'flag_reason'   => $flagReasons ? implode(', ', $flagReasons) : null,
        ]);

        return redirect()->route('seller.products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    /**
     * Form edit produk
     */
    public function edit(Product_seller $product)
    {
        // Pastikan hanya owner bisa edit
        if ($product->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403);
        }

        return view('SellerView.products.edit_seller', compact('product'));
    }

    /**
     * Update produk
     */
    public function update(Request $request, Product_seller $product)
    {
        // Cek owner
        if ($product->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403);
        }

        $request->validate([
            'nama_produk'   => 'required|string|max:255',
            'harga'         => 'required|numeric',
            'kategori'      => 'required|string|max:255',
            'jenis_produk'  => 'required|in:jual,sewa',
            'stok'          => 'required|integer|min:0',
            'deskripsi'     => 'nullable|string',
            'gambar'        => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $data = [
            'store_id'      => $product->store_id ?: Store::where('user_id', Auth::id())->value('id'),
            'name'          => $request->nama_produk,
            'nama_produk'   => $request->nama_produk,
            'price'         => $request->harga,
            'harga'         => $request->harga,
            'buy_price'     => $request->jenis_produk === 'jual' ? $request->harga : 0,
            'rent_price'    => $request->jenis_produk === 'sewa' ? $request->harga : 0,
            'category'      => $request->kategori,
            'kategori'      => $request->kategori,
            'jenis_produk'  => $request->jenis_produk,
            'is_rental'     => $request->jenis_produk === 'sewa',
            'stock'         => $request->stok,
            'stok'          => $request->stok,
            'description'   => $request->deskripsi,
            'deskripsi'     => $request->deskripsi,
        ];

        $flagReasons = \App\Models\Product::flagReasonsFor($data);
        $data['status'] = 'waiting';
        $data['flag_reason'] = $flagReasons ? implode(', ', $flagReasons) : null;

        // Jika upload gambar baru
        if ($request->hasFile('gambar')) {

            // Hapus gambar lama
            if ($product->gambar && Storage::disk('public')->exists($product->gambar)) {
                Storage::disk('public')->delete($product->gambar);
            }

            // Simpan gambar baru
            $data['gambar'] = $request->file('gambar')->store('products', 'public');
            $data['image'] = $data['gambar'];
        }

        $product->update($data);

        return redirect()->route('seller.products.index')->with('success', 'Produk berhasil diupdate!');
    }

    /**
     * Hapus produk
     */
    public function destroy(Product_seller $product)
    {
        if ($product->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403);
        }

        if ($product->gambar && Storage::disk('public')->exists($product->gambar)) {
            Storage::disk('public')->delete($product->gambar);
        }

        $product->delete();

        return redirect()
            ->route('seller.products.index')
            ->with('success', 'Produk berhasil dihapus!');
    }

    /**
     * Halaman khusus produk sewa
     * Hanya tampilkan produk dengan jenis_produk = sewa
     */
    public function rentals()
    {
        $rentals = Product_seller::where('user_id', \Illuminate\Support\Facades\Auth::id())
                    ->where('jenis_produk', 'sewa')
                    ->latest()
                    ->get();

        return view('SellerView.rentals.index_seller', compact('rentals'));
    }

    /**
     * Tampilkan detail produk (untuk customer)
     */
    public function show(Product_seller $product)
    {
        // Ambil rating produk
        $productRatings = \App\Models\SellerModels\ProductRating_seller::where('product_id', $product->id)
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        $avgProductRating = \App\Models\SellerModels\ProductRating_seller::getAverageRating($product->id);
        $productRatingCount = \App\Models\SellerModels\ProductRating_seller::getRatingCount($product->id);
        
        // Ambil rating toko
        $storeRatings = \App\Models\SellerModels\StoreRating_seller::where('store_id', $product->user_id)
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        $avgStoreRating = \App\Models\SellerModels\StoreRating_seller::getAverageRating($product->user_id);
        $storeRatingCount = \App\Models\SellerModels\StoreRating_seller::getRatingCount($product->user_id);
        
        return view('SellerView.products.show_seller', compact('product', 'productRatings', 'avgProductRating', 'productRatingCount', 'storeRatings', 'avgStoreRating', 'storeRatingCount'));
    }
}
