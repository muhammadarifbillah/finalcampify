<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderApiController extends Controller
{
    public function index()
    {
        return Order::with('product')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'buyer_id'   => 'required',
            'product_id' => 'required',
            'qty'   => 'required',
            'status'     => 'required'
        ]);

        return Order::create($data);
    }

    public function show(Order $order)
    {
        return $order->load('product');
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
