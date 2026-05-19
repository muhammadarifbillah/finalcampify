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

        return redirect('/seller/rentals')->with('success', 'Penyewaan berhasil diupdate');
    }

    public function approveRequest($id)
    {
        $rental = $this->sellerRentals()->findOrFail($id);
        $returnRequest = \App\Models\ReturnEscrow::where('order_id', $rental->order_id)->first();

        if ($returnRequest) {
            $returnRequest->update(['status' => 'approved']);
            $rental->update(['status' => 'approved_return']);
            return back()->with('success', 'Permintaan pengembalian sewa disetujui. Menunggu pembeli memasukkan resi pengiriman.');
        }

        return back()->with('error', 'Data pengembalian tidak ditemukan.');
    }

    public function receiveItem(Request $request, $id)
    {
        $rental = $this->sellerRentals()->findOrFail($id);
        $returnRequest = \App\Models\ReturnEscrow::where('order_id', $rental->order_id)->first();

        if (!$returnRequest) {
            return back()->with('error', 'Data pengembalian tidak ditemukan.');
        }

        $request->validate([
            'denda_kerusakan' => 'required|numeric|min:0',
            'kondisi_barang' => 'required|string|max:255',
        ]);

        // Set tanggal penerimaan barang
        $returnRequest->actual_date = now();
        $returnRequest->damage_fee = (string) $request->denda_kerusakan;
        $returnRequest->kondisi_barang = $request->kondisi_barang;
        $returnRequest->status = 'checking'; // sementara untuk auto calculation

        if (class_exists(\App\Services\ReturnSettlementService::class)) {
            $settlement = app(\App\Services\ReturnSettlementService::class);
            $settlement->applyAutoCalculations($returnRequest);
        }

        // Tentukan status akhir berdasarkan nilai defisit
        if ($returnRequest->deficit > 0) {
            $returnRequest->status = 'denda_pending';
            $rental->status = 'denda_pending';
            $returnRequest->save();
            $rental->save();

            return back()->with('success', 'Barang diterima. Denda melebihi deposit jaminan. Menunggu pembeli mentransfer sisa denda sebesar Rp ' . number_format($returnRequest->deficit, 0, ',', '.'));
        } else {
            $returnRequest->status = 'completed';
            $returnRequest->save();

            $rental->status = 'completed';
            $rental->save();

            if ($rental->order) {
                $rental->order->update(['status' => 'selesai']);
            }

            return back()->with('success', 'Barang diterima dalam kondisi baik / tercover deposit. Pengembalian sewa selesai otomatis.');
        }
    }

    public function verifyDendaPayment($id)
    {
        $rental = $this->sellerRentals()->findOrFail($id);
        $returnRequest = \App\Models\ReturnEscrow::where('order_id', $rental->order_id)->first();

        if (!$returnRequest) {
            return back()->with('error', 'Data pengembalian tidak ditemukan.');
        }

        $returnRequest->update(['status' => 'completed']);
        $rental->update(['status' => 'completed']);

        if ($rental->order) {
            $rental->order->update(['status' => 'selesai']);
        }

        return back()->with('success', 'Pembayaran sisa denda berhasil diverifikasi. Transaksi sewa selesai.');
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
}