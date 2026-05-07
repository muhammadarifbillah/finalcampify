<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Report;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::with('user')->latest()->get();
        $stores->each(function ($store) {
            $products = $this->sellerProductsQuery($store)->get(['id', 'status']);
            $store->admin_products_count = $products->count();
            $store->admin_approved_products_count = $products->where('status', 'approved')->count();
            $store->admin_waiting_products_count = $products->whereIn('status', ['waiting', 'pending'])->count();
        });

        return view('admin.stores', compact('stores'));
    }

    public function show($id)
    {
        $store = Store::with(['user', 'transactions'])->findOrFail($id);
        $sellerProducts = $this->sellerProductsQuery($store)->latest()->get();
        $pendingProducts = $sellerProducts->whereIn('status', ['waiting', 'pending']);
        $reports = Report::with(['reporter', 'product'])
            ->where(function ($query) use ($store) {
                $query->where('store_id', $store->id)
                    ->orWhere('seller_id', $store->user_id);
            })
            ->latest()
            ->get();

        // ✅ VERIFIKASI: Nama toko dan user harus sesuai dengan pengguna yang login
        // Admin dapat melihat semua toko, user hanya bisa melihat toko mereka sendiri
        if (auth()->check() && auth()->user()->role !== 'admin' && $store->user_id !== auth()->user()->id) {
            return back()->with('error', 'Anda tidak memiliki akses ke toko ini.');
        }

        // ✅ VERIFIKASI: Semua produk di toko dapat dilihat jika toko sudah diverifikasi
        $stats = [
            'total_products' => $sellerProducts->count(),
            'total_transactions' => $store->transactions()->count(),
            'total_sales' => $store->transactions()->sum('total'),
            'approved_products' => $sellerProducts->where('status', 'approved')->count(),
            'pending_products' => $sellerProducts->whereIn('status', ['waiting', 'pending'])->count(),
            'rejected_products' => $sellerProducts->where('status', 'rejected')->count(),
        ];

        // Riwayat aktivitas (simulasi)
        $activities = [
            ['type' => 'store_created', 'message' => 'Toko dibuat', 'date' => $store->created_at],
            ['type' => 'product_added', 'message' => 'Menambah ' . $stats['total_products'] . ' produk', 'date' => $store->updated_at],
        ];

        return view('admin.store_detail', compact('store', 'stats', 'activities', 'pendingProducts', 'reports', 'sellerProducts'));
    }

    public function approveProduct(Store $store, Product $product)
    {
        abort_unless($this->productBelongsToStore($store, $product), 403);

        $product->update([
            'store_id' => $product->store_id ?: $store->id,
            'status' => 'approved',
            'reviewed_by' => \Illuminate\Support\Facades\Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Produk disetujui.');
    }

    public function rejectProduct(Store $store, Product $product)
    {
        abort_unless($this->productBelongsToStore($store, $product), 403);

        $product->update([
            'store_id' => $product->store_id ?: $store->id,
            'status' => 'rejected',
            'reviewed_by' => \Illuminate\Support\Facades\Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Produk ditolak.');
    }

    // Aksi Admin untuk Seller Management
    public function approve($id)
    {
        Store::findOrFail($id)->update(['status' => 'active']);
        return back()->with('success', 'Seller berhasil diapprove.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        Store::findOrFail($id)->update([
            'status' => 'rejected',
            'catatan_admin' => $request->reason
        ]);

        return back()->with('success', 'Seller berhasil direject.');
    }

    public function suspend(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        Store::findOrFail($id)->update([
            'status' => 'suspended',
            'catatan_admin' => $request->reason
        ]);

        return back()->with('success', 'Seller berhasil disuspend.');
    }

    public function ban(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        Store::findOrFail($id)->update([
            'status' => 'banned',
            'catatan_admin' => $request->reason
        ]);

        return back()->with('success', 'Seller berhasil diban.');
    }

    public function activate($id)
    {
        Store::findOrFail($id)->update([
            'status' => 'active',
            'catatan_admin' => null
        ]);

        return back()->with('success', 'Seller berhasil diaktifkan kembali.');
    }

    // Legacy methods (untuk backward compatibility)
    public function banLegacy($id)
    {
        return $this->ban(request(), $id);
    }

    public function unban($id)
    {
        return $this->activate($id);
    }

    private function sellerProductsQuery(Store $store)
    {
        return Product::with(['store', 'seller', 'owner'])
            ->where(function ($query) use ($store) {
                $query->where('store_id', $store->id);

                if (Schema::hasColumn('products', 'user_id')) {
                    $query->orWhere('user_id', $store->user_id);
                }

                if (Schema::hasColumn('products', 'seller_id')) {
                    $query->orWhere('seller_id', $store->user_id);
                }
            });
    }

    private function productBelongsToStore(Store $store, Product $product): bool
    {
        return (int) $product->store_id === (int) $store->id
            || (int) $product->user_id === (int) $store->user_id
            || (int) $product->seller_id === (int) $store->user_id;
    }
}
