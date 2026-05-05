<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::with('user')->latest()->get();
        return view('admin.stores', compact('stores'));
    }

    public function show($id)
    {
        $store = Store::with(['user', 'products', 'transactions'])->findOrFail($id);

        // ✅ VERIFIKASI: Nama toko dan user harus sesuai dengan pengguna yang login
        // Admin dapat melihat semua toko, user hanya bisa melihat toko mereka sendiri
        if (auth()->check() && auth()->user()->role !== 'admin' && $store->user_id !== auth()->user()->id) {
            return back()->with('error', 'Anda tidak memiliki akses ke toko ini.');
        }

        // ✅ VERIFIKASI: Semua produk di toko dapat dilihat jika toko sudah diverifikasi
        $stats = [
            'total_products' => $store->products()->count(),
            'total_transactions' => $store->transactions()->count(),
            'total_sales' => $store->transactions()->sum('total'),
            'approved_products' => $store->products()->where('status', 'approved')->count(),
            'pending_products' => $store->products()->where('status', 'pending')->count(),
        ];

        // Riwayat aktivitas (simulasi)
        $activities = [
            ['type' => 'store_created', 'message' => 'Toko dibuat', 'date' => $store->created_at],
            ['type' => 'product_added', 'message' => 'Menambah ' . $stats['total_products'] . ' produk', 'date' => $store->updated_at],
        ];

        return view('admin.store_detail', compact('store', 'stats', 'activities'));
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
}