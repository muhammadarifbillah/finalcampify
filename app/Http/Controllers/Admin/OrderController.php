<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['buyer', 'details.product.store'])
            ->latest('created_at');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type (buy/rent)
        if ($request->filled('type')) {
            $query->whereHas('details', fn($q) => $q->where('type', $request->type));
        }

        // Filter by search (buyer name or order id)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                  ->orWhereHas('buyer', fn($q2) => $q2->where('name', 'like', "%$search%"));
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        $totalRevenue   = Order::sum('total');
        $totalOrders    = Order::count();
        $pendingOrders  = Order::where('status', 'menunggu')->count();
        $selesaiOrders  = Order::where('status', 'selesai')->count();

        return view('admin.orders', compact(
            'orders', 'totalRevenue', 'totalOrders', 'pendingOrders', 'selesaiOrders'
        ));
    }

    public function disbursements(Request $request)
    {
        $query = Order::with(['buyer', 'details.product.store'])
            ->where('status', 'selesai')
            ->latest('updated_at');

        // Filter by disbursement status
        if ($request->filled('status')) {
            if ($request->status === 'disbursed') {
                $query->where('is_disbursed', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_disbursed', false);
            }
        } else {
            $query->where('is_disbursed', false); // Default to pending
        }

        $orders = $query->paginate(15)->withQueryString();

        $totalTertahan = Order::where('status', 'selesai')->where('is_disbursed', false)->sum('total');
        $totalDicairkan = Order::where('is_disbursed', true)->sum('total');

        return view('admin.disbursements', compact('orders', 'totalTertahan', 'totalDicairkan'));
    }

    public function disburse(Order $order)
    {
        if ($order->status !== 'selesai') {
            return back()->with('error', 'Pesanan belum selesai. Dana tidak dapat dicairkan.');
        }

        if ($order->is_disbursed) {
            return back()->with('error', 'Dana untuk pesanan ini sudah dicairkan sebelumnya.');
        }

        $order->update([
            'is_disbursed' => true,
            'disbursed_at' => now()
        ]);

        return back()->with('success', 'Dana pesanan ' . ($order->order_number ?? '#'.$order->id) . ' berhasil ditandai telah dicairkan ke penjual.');
    }
}
