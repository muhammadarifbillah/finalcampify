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
        $pesanan = Order_pembeli::with('details.product')->where('user_id', \Illuminate\Support\Facades\Auth::id())->findOrFail($id);
        return view('pembeli.orders.detail_pembeli', compact('pesanan'));
    }

    public function returnForm($detail_id)
    {
        $detail = OrderDetail_pembeli::with('product')->findOrFail($detail_id);
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

        return view('pembeli.orders.return_pembeli', compact('detail', 'pesanan', 'denda', 'daysLate', 'endDate', 'return'));
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
            'resi_return' => 'required|string|max:255',
            'kondisi_barang' => 'nullable|string|max:50',
            'bukti_denda' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $startDate = Carbon::parse($detail->start_date)->startOfDay();
        $endDate = (clone $startDate)->addDays((int) $detail->duration)->startOfDay();
        $today = now()->startOfDay();

        $daysLate = max(0, $endDate->diffInDays($today, false));
        $denda = $daysLate * 10000;

        if ($denda > 0 && !$request->hasFile('bukti_denda')) {
            return back()
                ->withInput()
                ->withErrors(['bukti_denda' => 'Bukti pembayaran denda wajib diupload jika ada denda.']);
        }

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

        $buktiPath = null;
        if ($request->hasFile('bukti_denda')) {
            $buktiPath = $request->file('bukti_denda')->store('returns', 'public');
        }

        $return->fill([
            'resi_return' => $request->resi_return,
            'bukti_denda' => $buktiPath,
            'kondisi_barang' => $request->kondisi_barang ?: 'baik',
            'denda' => $denda,
            'tanggal_pengembalian' => now(),
        ]);
        $return->save();

        $rental->status = 'returned';
        $rental->save();

        return redirect()
            ->route('orders.detail', $pesanan->id)
            ->with('success', 'Pengembalian berhasil disubmit.');
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
