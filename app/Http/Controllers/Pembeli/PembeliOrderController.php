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
        $dailyFine = (int) config('returns.daily_fine', 50000);
        $denda = $daysLate * $dailyFine;

        $return = Return_pembeli::query()
            ->where('order_id', $pesanan->id)
            ->where('type', 'sewa')
            ->first();

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

        $return = Return_pembeli::firstOrNew([
            'order_id' => $pesanan->id,
            'type' => 'sewa',
        ]);

        if ($return->exists) {
            return back()->with('error', 'Pengembalian untuk sewa ini sudah pernah disubmit.');
        }

        // Initialize with basic data
        $return->fill([
            'type' => 'sewa',
            'status' => 'pending',
            'escrow_total' => (string) ((int) ($pesanan->total ?? 0)),
            'expected_date' => $endDate,
            'late_fee' => '0',
            'damage_fee' => '0',
            'to_seller' => '0',
            'to_buyer' => '0',
        ]);

        // Use settlement service to calculate breakdown (Rental Fee vs Deposit 50%)
        $settlement = app(\App\Services\ReturnSettlementService::class);
        $settlement->applyAutoCalculations($return);
        
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
