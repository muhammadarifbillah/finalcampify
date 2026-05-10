<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pembeli\OrderDetail_pembeli;
use App\Models\Pembeli\Order_pembeli;
use App\Models\Pembeli\Rental_pembeli;
use App\Models\Pembeli\Return_pembeli;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReturnController extends Controller
{
    public function checkReturn($detail_id)
    {
        $detail = OrderDetail_pembeli::with('product.store')->findOrFail($detail_id);
        $pesanan = Order_pembeli::findOrFail($detail->order_id);
        
        if ($pesanan->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($detail->type !== 'rent') {
            return response()->json(['message' => 'Produk ini bukan produk sewa'], 400);
        }

        $startDate = Carbon::parse($detail->start_date)->startOfDay();
        $endDate = (clone $startDate)->addDays((int) $detail->duration)->startOfDay();
        $today = now()->startOfDay();

        $daysLate = max(0, $endDate->diffInDays($today, false));
        $denda = $daysLate * 10000;

        return response()->json([
            'success' => true,
            'data' => [
                'detail' => $detail,
                'days_late' => $daysLate,
                'fine_amount' => $denda,
                'due_date' => $endDate->toDateString(),
            ]
        ]);
    }

    public function storeReturn(Request $request, $detail_id)
    {
        $detail = OrderDetail_pembeli::with('product')->findOrFail($detail_id);
        $pesanan = Order_pembeli::findOrFail($detail->order_id);

        if ($pesanan->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($detail->type !== 'rent') {
            return response()->json(['message' => 'Produk ini bukan produk sewa'], 400);
        }

        $request->validate([
            'metode_return' => 'required|in:antar,kurir',
            'resi_return' => 'required_if:metode_return,kurir|nullable|string|max:255',
            'foto_kondisi' => 'required|file|mimes:jpg,jpeg,png,webp,mp4,mov,avi|max:10240',
        ]);

        $resi = $request->metode_return === 'antar' ? 'DIANTAR_LANGSUNG' : $request->resi_return;

        $fotoKondisiPath = null;
        if ($request->hasFile('foto_kondisi')) {
            $fotoKondisiPath = $request->file('foto_kondisi')->store('returns/conditions', 'public');
        }

        $startDate = Carbon::parse($detail->start_date)->startOfDay();
        $endDate = (clone $startDate)->addDays((int) $detail->duration)->startOfDay();

        $rental = Rental_pembeli::firstOrCreate(
            [
                'user_id' => $pesanan->user_id,
                'product_id' => $detail->product_id,
                'order_id' => $pesanan->id,
                'start_date' => $startDate->toDateString(),
            ],
            [
                'end_date' => $endDate->toDateString(),
                'duration' => (int) $detail->duration,
                'price' => (int) $detail->harga,
                'status' => 'active',
            ]
        );

        $return = Return_pembeli::firstOrNew(['rental_id' => $rental->id]);

        if ($return->exists) {
            return response()->json(['message' => 'Pengembalian sudah pernah diajukan'], 422);
        }

        $return->fill([
            'resi_return' => $resi,
            'proof_returned_image' => $fotoKondisiPath,
            'tanggal_pengembalian' => now(),
            'denda' => 0,
            'kondisi_barang' => 'baik',
        ]);
        $return->save();

        $rental->status = 'returned';
        $rental->save();

        return response()->json([
            'success' => true,
            'message' => 'Pengembalian berhasil diajukan',
            'data' => $return
        ], 201);
    }

    public function uploadFinePayment(Request $request, $return_id)
    {
        $return = Return_pembeli::findOrFail($return_id);
        $rental = Rental_pembeli::findOrFail($return->rental_id);

        if ($rental->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'bukti_denda' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('bukti_denda')) {
            $buktiPath = $request->file('bukti_denda')->store('returns', 'public');
            $return->update(['bukti_denda' => $buktiPath]);
            
            $rental->update(['status' => 'denda_dibayar']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Bukti pembayaran denda berhasil diunggah',
            'data' => $return
        ]);
    }
}
