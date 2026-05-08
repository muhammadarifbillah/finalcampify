<?php
namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Pembeli\OrderDetail_pembeli;
use Illuminate\Http\Request;
use App\Models\Pembeli\Order_pembeli;
use App\Models\Pembeli\Rental_pembeli;
use App\Models\Pembeli\Return_pembeli;
use Carbon\Carbon;

class PembeliOrderController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $orders = Order_pembeli::where('user_id', $user->id)->latest()->get();
        return view('pembeli.orders.index_pembeli', compact('orders', 'user'));
    }

    public function detail($id)
    {
        $pesanan = Order_pembeli::with('details.product.store')->where('user_id', \Illuminate\Support\Facades\Auth::id())->findOrFail($id);
        return view('pembeli.orders.detail_pembeli', compact('pesanan'));
    }

    public function returnForm($detail_id)
    {
        $detail = OrderDetail_pembeli::with('product.store')->findOrFail($detail_id);
        $pesanan = Order_pembeli::findOrFail($detail->order_id);
        
        // Cek kepemilikan
        if ($pesanan->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403);
        }

        if ($detail->type !== 'rent') {
            abort(404);
        }

        $startDate = Carbon::parse($detail->start_date)->startOfDay();
        $endDate = (clone $startDate)->addDays((int) $detail->duration)->startOfDay();
        $today = now()->startOfDay();

        $daysLate = max(0, $endDate->diffInDays($today, false));
        $denda = $daysLate * 10000;

        $rental = Rental_pembeli::query()
            ->where('order_id', $pesanan->id)
            ->where('product_id', $detail->product_id)
            ->whereDate('start_date', $startDate)
            ->first();

        $return = $rental?->returnRequest;

        return view('pembeli.orders.return_pembeli', compact('detail', 'pesanan', 'denda', 'daysLate', 'endDate', 'return', 'rental'));
    }

    public function returnStore(Request $request, $detail_id)
    {
        $detail = OrderDetail_pembeli::with('product')->findOrFail($detail_id);
        $pesanan = Order_pembeli::findOrFail($detail->order_id);

        if ($pesanan->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403);
        }

        if ($detail->type !== 'rent') {
            abort(404);
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
            return back()->with('error', 'Pengembalian untuk sewa ini sudah pernah disubmit.');
        }

        $return->fill([
            'resi_return' => $resi,
            'foto_kondisi' => $fotoKondisiPath,
            'tanggal_pengembalian' => now(),
            'denda' => 0, // Akan diisi oleh seller
            'kondisi_barang' => 'baik', // Akan diupdate oleh seller
        ]);
        $return->save();

        $rental->status = 'returned';
        $rental->save();

        return redirect()
            ->route('orders.detail', $pesanan->id)
            ->with('success', 'Resi pengembalian berhasil dikirim. Menunggu pengecekan toko.');
    }

    public function uploadBuktiDenda(Request $request, $return_id)
    {
        $return = Return_pembeli::findOrFail($return_id);
        $rental = Rental_pembeli::findOrFail($return->rental_id);

        if ($rental->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403);
        }

        $request->validate([
            'bukti_denda' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('bukti_denda')) {
            $buktiPath = $request->file('bukti_denda')->store('returns', 'public');
            $return->update(['bukti_denda' => $buktiPath]);
            
            $rental->update(['status' => 'denda_dibayar']);
        }

        return back()->with('success', 'Bukti pembayaran denda berhasil diunggah. Menunggu verifikasi toko.');
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
