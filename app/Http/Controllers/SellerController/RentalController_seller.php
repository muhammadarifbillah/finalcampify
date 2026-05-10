<?php

namespace App\Http\Controllers\SellerController;

use App\Http\Controllers\Controller;
use App\Models\SellerModels\Rental_seller;
use App\Models\SellerModels\Product_seller;
use Illuminate\Http\Request;

class RentalController_seller extends Controller
{
    public function index()
    {
        $rentals = $this->sellerRentals()->latest()->get();
        return view('SellerView.rentals.index_seller', compact('rentals'));
    }

    public function show($id)
    {
        $rental = $this->sellerRentals()->findOrFail($id);
        return view('SellerView.rentals.show_seller', compact('rental'));
    }

    public function edit($id)
    {
        $rental = $this->sellerRentals()->findOrFail($id);

        // Hitung denda telat otomatis
        $endDate = \Carbon\Carbon::parse($rental->end_date)->startOfDay();
        $today = now()->startOfDay();
        $daysLate = max(0, $endDate->diffInDays($today, false));
        $dendaTelat = $daysLate * 10000;

        return view('SellerView.rentals.edit_seller', compact('rental', 'dendaTelat', 'daysLate'));
    }

    public function update(Request $request, $id)
    {
        $rental = $this->sellerRentals()->findOrFail($id);

        $rental->update([
            'status' => $request->status,
            'catatan' => $request->catatan ?? $rental->catatan
        ]);

        // Jika ada data return (pengembalian), update denda dan kondisi
        if ($rental->returnRequest) {
            // Hitung ulang denda telat
            $endDate = \Carbon\Carbon::parse($rental->end_date)->startOfDay();
            $today = now()->startOfDay();
            $daysLate = max(0, $endDate->diffInDays($today, false));
            $dendaTelat = $daysLate * 10000;

            $dendaKerusakan = $request->denda_kerusakan ?? 0;
            $totalDenda = $dendaTelat + $dendaKerusakan;

            $rental->returnRequest->update([
                'denda' => $totalDenda,
                'kondisi_barang' => $request->kondisi_barang ?? 'baik',
            ]);

            // Jika status diset ke denda_pending, pastikan denda > 0
            if ($request->status === 'denda_pending' && $totalDenda <= 0) {
                return back()->with('error', 'Tidak ada denda yang perlu dibayar (Telat: 0, Kerusakan: 0).');
            }
        }

        // Sync status ke tabel orders agar pembeli melihat perubahan
        if ($rental->order) {
            if ($request->status === 'active') {
                $rental->order->update(['status' => 'diproses']);
            } elseif ($request->status === 'completed') {
                $rental->order->update(['status' => 'selesai']);
            } elseif ($request->status === 'cancelled') {
                $rental->order->update(['status' => 'dibatalkan']);
            }
        }

        return redirect('/seller/rentals')->with('success', 'Penyewaan berhasil diupdate');
    }

    public function verifyUserKtp($userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        
        $user->update([
            'ktp_verified_at' => now()
        ]);

        return back()->with('success', 'Identitas Pembeli Berhasil Diverifikasi! Pesanan kini dapat diproses.');
    }

    private function sellerRentals()
    {
        return Rental_seller::with(['product', 'user', 'order'])
            ->whereHas('product', function ($query) {
                $query->where('user_id', \Illuminate\Support\Facades\Auth::id())
                    ->orWhereHas('store', fn ($store) => $store->where('user_id', \Illuminate\Support\Facades\Auth::id()));
            });
    }
}       return Rental_seller::with(['product', 'user', 'order'])
            ->whereHas('product', function ($query) {
                $query->where('user_id', \Illuminate\Support\Facades\Auth::id())
                    ->orWhereHas('store', fn ($store) => $store->where('user_id', \Illuminate\Support\Facades\Auth::id()));
            });