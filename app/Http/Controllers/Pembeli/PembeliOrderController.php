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

        $request->validate([
            'metode_return' => 'required|in:antar,kurir',
            'resi_return' => 'required_if:metode_return,kurir|nullable|string|max:255',
            'foto_kondisi' => 'required|file|mimes:jpg,jpeg,png,webp,mp4,mov,avi|max:10240',
            'alasan_return' => $detail->type === 'buy' ? 'required|string|max:500' : 'nullable',
        ]);

        $resi = $request->metode_return === 'antar' ? 'DIANTAR_LANGSUNG' : $request->resi_return;

        $fotoKondisiPath = null;
        if ($request->hasFile('foto_kondisi')) {
            $file = $request->file('foto_kondisi');
            $filename = 'return_' . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/images'), $filename);
            $fotoKondisiPath = 'assets/images/' . $filename;
        }

        $rental = null;
        if ($detail->type === 'rent') {
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
        }

        $returnType = $detail->type === 'rent' ? 'sewa' : 'jual_beli';
        
        $return = Return_pembeli::firstOrNew([
            'order_id' => $pesanan->id,
            'type' => $returnType,
        ]);

        if ($return->exists) {
            return back()->with('error', 'Pengembalian untuk pesanan ini sudah pernah disubmit.');
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

        // Use settlement service if exists
        $return->setRelation('order', $pesanan->loadMissing('details.product'));

        if (class_exists(ReturnSettlementService::class)) {
            $settlement = app(ReturnSettlementService::class);
            $settlement->applyAutoCalculations($return);
        }
        
        $return->save();

        if ($rental) {
            $rental->status = 'returned';
            $rental->save();
        }

        $message = $detail->type === 'buy' ? 'Permintaan retur berhasil dikirim. Menunggu mediasi Admin.' : 'Resi pengembalian berhasil dikirim. Menunggu pengecekan toko.';
        return redirect()
            ->route('orders.detail', $pesanan->id)
            ->with('success', $message);
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
            
            $return->update(['bukti_denda' => 'assets/images/' . $filename]);
            
            $rental->update(['status' => 'denda_dibayar']);
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
