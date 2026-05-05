<?php

namespace App\Http\Controllers\SellerController;

use App\Http\Controllers\Controller;
use App\Models\SellerModels\Product_seller;
use App\Models\SellerModels\Order_seller;
use Illuminate\Http\Request;

class OrderController_seller extends Controller
{
    public function index()
    {
        $orders = $this->sellerOrders()->latest()->get();
        return view('SellerView.orders.index_seller', compact('orders'));
    }

    public function show($id)
    {
        $order = $this->sellerOrders()->findOrFail($id);
        return view('SellerView.orders.show_seller', compact('order'));
    }

    public function edit($id)
    {
        $order = $this->sellerOrders()->findOrFail($id);
        return view('SellerView.orders.edit_seller', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $order = $this->sellerOrders()->findOrFail($id);

        $order->update([
            'status' => $request->status,
            'no_resi' => $request->resi ?? $request->no_resi,
        ]);

        return redirect('/seller/orders')->with('success', 'Pesanan berhasil diupdate');
    }

    public function updateStatus(Request $request, $order)
    {
        $request->validate([
            'status' => 'required|in:menunggu,diproses,dikirim,selesai,dibatalkan',
        ]);

        $order = $this->sellerOrders()->findOrFail($order);
        $order->update(['status' => $request->status]);

        return back()->with('success', 'Status pesanan berhasil diupdate');
    }

    public function updateResi(Request $request, $order)
    {
        $request->validate([
            'resi' => 'required|string|max:255',
        ]);

        $order = $this->sellerOrders()->findOrFail($order);
        $order->update([
            'no_resi' => $request->resi,
            'status' => $order->status === 'selesai' ? 'selesai' : 'dikirim',
        ]);

        return back()->with('success', 'Resi berhasil diupdate');
    }

    private function sellerOrders()
    {
        return Order_seller::with(['details.product', 'product', 'buyer'])
            ->whereHas('details.product', function ($query) {
                $query->where('user_id', \Illuminate\Support\Facades\Auth::id())
                    ->orWhereHas('store', fn ($store) => $store->where('user_id', \Illuminate\Support\Facades\Auth::id()));
            });
    }
}
