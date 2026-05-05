<?php

namespace App\Http\Controllers\Api_seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderApiController extends Controller
{
    public function index()
    {
        return Order::with(['buyer', 'details.product'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required_without:buyer_id|exists:users,id',
            'buyer_id' => 'required_without:user_id|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
            'status' => 'nullable|in:menunggu,diproses,dikirim,selesai,dibatalkan',
        ]);

        $product = Product::findOrFail($data['product_id']);
        $qty = (int) $data['qty'];
        $price = (int) ($product->buy_price ?: $product->price ?: $product->harga ?: 0);

        $order = Order::create([
            'user_id' => $data['user_id'] ?? $data['buyer_id'],
            'total' => $price * $qty,
            'status' => $data['status'] ?? 'menunggu',
        ]);

        OrderDetail::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'qty' => $qty,
            'harga' => $price,
            'type' => 'buy',
        ]);

        return $order->load(['buyer', 'details.product']);
    }

    public function show(Order $order)
    {
        return $order->load(['buyer', 'details.product']);
    }

    public function update(Request $request, Order $order)
    {
        $order->update($request->all());
        return $order;
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(['message'=>'Order dihapus']);
    }
}


