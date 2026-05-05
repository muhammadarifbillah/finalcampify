<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['buyer', 'details.product'])->latest()->get();

        return view('admin.orders', compact('orders'));
    }
}
