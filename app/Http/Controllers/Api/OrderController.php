<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('orderDetails.product')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_name' => 'required|string|max:255',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string',
            'shipping_district' => 'required|string',
            'shipping_postal_code' => 'required|string',
            'shipping_phone' => 'required|string',
            'metode_pembayaran' => 'required|string|in:transfer,cod',
            'kurir' => 'required|string',
            'bukti_pembayaran' => 'required_if:metode_pembayaran,transfer|nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.type' => 'required|in:buy,rent',
            'items.*.duration' => 'nullable|integer',
            'items.*.start_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            DB::beginTransaction();

            $total = 0;
            $orderItems = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                $price = $item['type'] === 'buy' 
                    ? ($product->buy_price ?? $product->harga)
                    : (($product->rent_price ?? $product->harga) * ($item['duration'] ?? 1));

                $subtotal = $price * $item['qty'];
                $total += $subtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'harga' => $price,
                    'type' => $item['type'],
                    'duration' => $item['duration'] ?? null,
                    'start_date' => $item['start_date'] ?? null,
                ];
            }

            // Add shipping cost (dummy for now, can be passed from mobile)
            $shippingCost = $request->shipping_cost ?? 15000;
            $total += $shippingCost;

            $buktiPath = null;
            if ($request->hasFile('bukti_pembayaran')) {
                $file = $request->file('bukti_pembayaran');
                $filename = time() . '_api_' . Auth::id() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/pembayaran'), $filename);
                $buktiPath = 'uploads/pembayaran/' . $filename;
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'receiver_name' => $request->receiver_name,
                'total' => $total,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_district' => $request->shipping_district,
                'shipping_postal_code' => $request->shipping_postal_code,
                'shipping_phone' => $request->shipping_phone,
                'metode_pembayaran' => $request->metode_pembayaran,
                'kurir' => $request->kurir,
                'status' => 'diproses',
                'bukti_pembayaran' => $buktiPath,
            ]);

            foreach ($orderItems as $orderItem) {
                $orderItem['order_id'] = $order->id;
                OrderDetail::create($orderItem);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat',
                'data' => $order->load('orderDetails.product')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $order = Order::with('orderDetails.product')
            ->where('user_id', Auth::id())
            ->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }
}
