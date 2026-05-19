<?php
namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Pembeli\OrderDetail_pembeli;
use Illuminate\Http\Request;
use App\Models\Pembeli\Order_pembeli;
use App\Models\Pembeli\Rental_pembeli;
use App\Models\Pembeli\Return_pembeli;
use App\Services\ReturnSettlementService;
use Carbon\Carbon;

class PembeliOrderController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $orders = Order_pembeli::where('user_id', $user->id)->latest()->get();
        return view('pembeli.orders.index_pembeli', compact('orders', 'user'));
    }

    public function detail($id, Request $request)
    {
        $userId = \Illuminate\Support\Facades\Auth::id();
        $detailId = $request->query('detail_id');

        $query = Order_pembeli::with(['details' => function($q) use ($detailId) {
            if ($detailId) {
                $q->where('id', $detailId);
            }
        }, 'details.product.store'])->where('user_id', $userId);

        $pesanan = $query->findOrFail($id);
        
        return view('pembeli.orders.detail_pembeli', compact('pesanan', 'detailId'));
    }

    public function returnForm($detail_id)
    {
        $detail = OrderDetail_pembeli::with('product.store')->findOrFail($detail_id);
        $pesanan = Order_pembeli::findOrFail($detail->order_id);
        
        // Cek kepemilikan
        if ($pesanan->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403);
        }

        $denda = 0;
        $daysLate = 0;
        $endDate = null;

        if ($detail->type === 'rent') {
            $startDate = Carbon::parse($detail->start_date)->startOfDay();
            $endDate = (clone $startDate)->addDays((int) $detail->duration)->startOfDay();
            $today = now()->startOfDay();

            $daysLate = max(0, $endDate->diffInDays($today, false));
            $dailyFine = (int) config('returns.daily_fine', 50000);
            $denda = $daysLate * $dailyFine;
        }

        $return = Return_pembeli::query()
            ->where('order_id', $pesanan->id)
            ->where('type', 'sewa')
            ->first();

        $rental = Rental_pembeli::where('order_id', $pesanan->id)->first();

        return view('pembeli.orders.return_pembeli', compact('detail', 'pesanan', 'denda', 'daysLate', 'endDate', 'return', 'rental'));
    }

    public function returnStore(Request $request, $detail_id)
    {
        $detail = OrderDetail_pembeli::with('product')->findOrFail($detail_id);
        $pesanan = Order_pembeli::findOrFail($detail->order_id);

        if ($pesanan->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403);
        }

        if ($detail->type === 'rent') {
            $request->validate([
                'metode_return' => 'required|in:antar,kurir',
                'buyer_refund_bank_name' => 'required|string|max:255',
                'buyer_refund_bank_account' => 'required|string|max:255',
                'buyer_refund_bank_name_owner' => 'required|string|max:255',
            ]);

            $depositAmount = ($detail->product->buy_price ?? 0) * 0.25;
            $rentalFeeAmount = $detail->harga;
            $escrowTotal = $depositAmount + $rentalFeeAmount;

            $startDate = Carbon::parse($detail->start_date)->startOfDay();
            $endDate = (clone $startDate)->addDays((int) $detail->duration)->startOfDay();

            $rental = Rental_pembeli::firstOrCreate(
                [
                    'user_id' => $pesanan->user_id,
                    'product_id' => $detail->product_id,
                    'order_id' => $pesanan->id,
                ],
                [
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'duration' => (int) $detail->duration,
                    'price' => (int) $detail->harga,
                    'status' => 'active',
                ]
            );

            $return = Return_pembeli::firstOrNew([
                'order_id' => $pesanan->id,
                'type' => 'sewa',
            ]);

            if ($return->exists) {
                return back()->with('error', 'Pengembalian untuk pesanan ini sudah pernah diajukan.');
            }

            if ($request->metode_return === 'antar') {
                $adminFee = $rentalFeeAmount * 0.1; // 10% admin fee
                $return->fill([
                    'rental_id' => $rental->id,
                    'resi_return' => 'DIANTAR_LANGSUNG',
                    'proof_returned_image' => null,
                    'tanggal_pengembalian' => now(),
                    'actual_date' => now(),
                    'denda' => 0,
                    'kondisi_barang' => 'baik',
                    'status' => 'completed',
                    'escrow_total' => (string) $escrowTotal,
                    'deposit_amount' => (string) $depositAmount,
                    'rental_fee_amount' => (string) $rentalFeeAmount,
                    'expected_date' => $endDate,
                    'late_fee' => '0',
                    'damage_fee' => '0',
                    'to_seller' => (string) ($rentalFeeAmount - $adminFee),
                    'to_buyer' => (string) $depositAmount,
                    'total_fines' => '0',
                    'deficit' => '0',
                    'buyer_refund_bank_name' => $request->buyer_refund_bank_name,
                    'buyer_refund_bank_account' => $request->buyer_refund_bank_account,
                    'buyer_refund_bank_name_owner' => $request->buyer_refund_bank_name_owner,
                    'renter_notes' => 'Pengembalian Antar Langsung ke Toko.',
                ]);
                $return->save();

                $rental->status = 'completed';
                $rental->save();

                $pesanan->status = 'selesai';
                $pesanan->save();

                return redirect()
                    ->route('orders.detail', $pesanan->id)
                    ->with('success', 'Pengembalian sewa (Antar Langsung) berhasil diajukan dan diselesaikan.');
            } else {
                $return->fill([
                    'rental_id' => $rental->id,
                    'resi_return' => null,
                    'proof_returned_image' => null,
                    'tanggal_pengembalian' => null,
                    'actual_date' => null,
                    'denda' => 0,
                    'kondisi_barang' => 'belum_dicek',
                    'status' => 'pending',
                    'escrow_total' => (string) $escrowTotal,
                    'deposit_amount' => (string) $depositAmount,
                    'rental_fee_amount' => (string) $rentalFeeAmount,
                    'expected_date' => $endDate,
                    'late_fee' => '0',
                    'damage_fee' => '0',
                    'to_seller' => '0',
                    'to_buyer' => '0',
                    'total_fines' => '0',
                    'deficit' => '0',
                    'buyer_refund_bank_name' => $request->buyer_refund_bank_name,
                    'buyer_refund_bank_account' => $request->buyer_refund_bank_account,
                    'buyer_refund_bank_name_owner' => $request->buyer_refund_bank_name_owner,
                    'renter_notes' => null,
                ]);
                $return->save();

                $rental->status = 'returning';
                $rental->save();

                return redirect()
                    ->route('orders.detail', $pesanan->id)
                    ->with('success', 'Permintaan pengembalian sewa berhasil diajukan. Menunggu persetujuan Seller.');
            }
        }

        // --- Alur Retur Jual Beli / Buy Return (Tetap/Legacy) ---
        $request->validate([
            'metode_return' => 'required|in:antar,kurir',
            'resi_return' => 'required_if:metode_return,kurir|nullable|string|max:255',
            'foto_kondisi' => 'required|file|mimes:jpg,jpeg,png,webp,mp4,mov,avi|max:10240',
            'alasan_return' => 'required|string|max:500',
        ]);

        $resi = $request->metode_return === 'antar' ? 'DIANTAR_LANGSUNG' : $request->resi_return;

        $fotoKondisiPath = null;
        if ($request->hasFile('foto_kondisi')) {
            $file = $request->file('foto_kondisi');
            $filename = 'return_' . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/images'), $filename);
            $fotoKondisiPath = 'assets/images/' . $filename;
        }

        $return = Return_pembeli::firstOrNew([
            'order_id' => $pesanan->id,
            'type' => 'jual_beli',
        ]);

        if ($return->exists) {
            return back()->with('error', 'Pengembalian untuk pesanan ini sudah pernah diajukan.');
        }

        $return->fill([
            'rental_id' => $rental?->id,
            'resi_return' => $resi,
            'proof_returned_image' => $fotoKondisiPath,
            'tanggal_pengembalian' => now(),
            'actual_date' => now(),
            'denda' => 0, 
            'kondisi_barang' => $detail->type === 'buy' ? 'dispute' : 'baik',
            'status' => $detail->type === 'buy' ? 'dispute' : 'pending',
            'escrow_total' => (string) ($detail->type === 'buy' ? ($detail->harga * $detail->qty) : ($pesanan->total ?? 0)),
            'expected_date' => $detail->type === 'rent' ? (isset($endDate) ? $endDate : null) : null,
            'late_fee' => '0',
            'damage_fee' => '0',
            'to_seller' => '0',
            'to_buyer' => '0',
            'renter_notes' => $request->alasan_return,
        ]);
        $return->save();

        $pesanan->status = 'retur';
        $pesanan->save();

        return redirect()
            ->route('orders.detail', $pesanan->id)
            ->with('success', 'Permintaan retur berhasil dikirim. Menunggu mediasi Admin.');
    }

    public function submitShipping(Request $request, $return_id)
    {
        $return = Return_pembeli::findOrFail($return_id);
        $pesanan = Order_pembeli::findOrFail($return->order_id);

        if ($pesanan->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403);
        }

        $request->validate([
            'resi_return' => 'required|string|max:255',
            'foto_kondisi' => 'required|file|mimes:jpg,jpeg,png,webp,mp4,mov,avi|max:10240',
            'renter_notes' => 'nullable|string|max:500',
        ]);

        $fotoKondisiPath = null;
        if ($request->hasFile('foto_kondisi')) {
            $file = $request->file('foto_kondisi');
            $filename = 'return_' . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/images'), $filename);
            $fotoKondisiPath = 'assets/images/' . $filename;
        }

        $return->update([
            'resi_return' => $request->resi_return,
            'proof_returned_image' => $fotoKondisiPath,
            'renter_notes' => $request->renter_notes,
            'status' => 'shipping',
            'tanggal_pengembalian' => now(),
        ]);

        return back()->with('success', 'Detail pengiriman balik berhasil disubmit. Menunggu Seller menerima barang.');
    }

    public function uploadBuktiDenda(Request $request, $return_id)
    {
        $return = Return_pembeli::findOrFail($return_id);
        $pesanan = Order_pembeli::findOrFail($return->order_id);
        $rental = Rental_pembeli::where('order_id', $pesanan->id)->firstOrFail();

        if ($pesanan->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403);
        }

        $request->validate([
            'bukti_denda' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('bukti_denda')) {
            $file = $request->file('bukti_denda');
            $filename = 'denda_' . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/images'), $filename);

            $return->update([
                'bukti_denda' => 'assets/images/' . $filename,
                'status' => 'denda_submitted'
            ]);

            $rental->update(['status' => 'denda_submitted']);
        }

        return back()->with('success', 'Bukti pembayaran denda berhasil diunggah. Menunggu verifikasi toko.');
    }

    public function confirmReceipt($id)
    {
        $pesanan = Order_pembeli::where('id', $id)->where('user_id', \Illuminate\Support\Facades\Auth::id())->firstOrFail();
        
        if ($pesanan->status === 'dikirim') {
            $pesanan->status = 'selesai';
            $pesanan->save();

            // Aktifkan rental jika ada
            $rentals = Rental_pembeli::where('order_id', $pesanan->id)->get();
            foreach ($rentals as $rental) {
                if ($rental->status === 'pending') {
                    $rental->status = 'active';
                    $rental->save();
                }
            }

            return back()->with('success', 'Pesanan telah diterima. Terima kasih telah berbelanja!');
        }
        
        return back()->with('error', 'Pesanan tidak dapat dikonfirmasi saat ini.');
    }

    public function cancel($id)
    {
        $pesanan = Order_pembeli::where('id', $id)->where('user_id', \Illuminate\Support\Facades\Auth::id())->firstOrFail();
        if (in_array($pesanan->status, ['menunggu', 'diproses'])) {
            $pesanan->status = 'dibatalkan';
            $pesanan->save();
            return back()->with('success', 'Pesanan dibatalkan');
        }
        return back()->with('error', 'Pesanan tidak dapat dibatalkan');
    }
}
