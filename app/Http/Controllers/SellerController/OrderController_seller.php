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
        $orders = Order_seller::with(['product', 'buyer'])->get();
        return view('SellerView.orders.index_seller', compact('orders'));
    }

    public function show($id)
    {
        $order = Order_seller::with(['product', 'buyer'])->findOrFail($id);
        return view('SellerView.orders.show_seller', compact('order'));
    }

    public function edit($id)
    {
        $order = Order_seller::with(['product', 'buyer'])->findOrFail($id);
        return view('SellerView.orders.edit_seller', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $order = Order_seller::findOrFail($id);

        $order->update([
            'status' => $request->status,
            'resi' => $request->resi
        ]);

        return redirect('/seller/orders')->with('success', 'Pesanan berhasil diupdate');
    }
}
