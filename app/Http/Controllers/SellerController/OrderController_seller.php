<?php

namespace App\Http\Controllers\SellerController;

use App\Http\Controllers\Controller;
use App\Models\SellerOrder;
use App\Services\SellerOrderService;
use Illuminate\Http\Request;

class OrderController_seller extends Controller
{
    public function __construct(private SellerOrderService $sellerOrderService)
    {
    }

    public function index(Request $request)
    {
        $query = $this->sellerOrders();

        if ($request->filled('status')) {
            $query->where('status', $this->sellerOrderService->mapOrderStatus($request->status));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                  ->orWhere('seller_order_number', 'like', '%' . $search . '%')
                  ->orWhereHas('order', function ($oq) use ($search) {
                      $oq->where('order_number', 'like', '%' . $search . '%')
                          ->orWhere('receiver_name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('order.buyer', function ($bq) use ($search) {
                      $bq->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('items.product', function ($pq) use ($search) {
                      $pq->where('nama_produk', 'like', '%' . $search . '%');
                  });
            });
        }

        $orders = $query->latest()->get();
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

        // Map English status to Indonesian database values if necessary
        $statusMap = [
            'menunggu' => 'pending',
            'diproses' => 'processing',
            'dikirim' => 'shipped',
            'selesai' => 'delivered',
            'pending' => 'pending',
            'processing' => 'processing',
            'shipped' => 'shipped',
            'delivered' => 'delivered',
            'completed' => 'delivered',
            'cancelled' => 'cancelled',
            'dibatalkan' => 'cancelled',
        ];

        $request->validate([
            'status' => 'required',
            'resi' => 'nullable|string|max:255',
            'video_pengiriman' => 'nullable|file|mimes:mp4,mov,avi|max:20480',
        ]);

        $status = $statusMap[$request->status] ?? $request->status;
        $resi = $request->resi ?? $request->no_resi;

        $updateData = [
            'status' => $status,
            'no_resi' => $resi,
            'shipped_at' => $status === SellerOrder::STATUS_SHIPPED ? ($order->shipped_at ?? now()) : $order->shipped_at,
            'delivered_at' => $status === SellerOrder::STATUS_DELIVERED ? ($order->delivered_at ?? now()) : $order->delivered_at,
        ];

        // If status changing to 'dikirim' or already 'dikirim'/'selesai' and a video is uploaded
        if ($request->hasFile('video_pengiriman')) {
            $file = $request->file('video_pengiriman');
            $updateData['video_pengiriman_hash'] = md5_file($file->getRealPath());
            $filename = 'kirim_' . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/videos'), $filename);
            $updateData['video_pengiriman'] = 'assets/videos/' . $filename;
        } else if ($status === SellerOrder::STATUS_SHIPPED && !$order->video_pengiriman) {
             return back()->with('error', 'Video bukti pengiriman wajib diunggah saat mengubah status menjadi Dikirim.');
        }

        $order->update($updateData);
        app(\App\Services\SellerOrderService::class)->syncPayout($order->fresh(['payout']));
        $this->sellerOrderService->syncParentOrderStatus($order->order);

        return redirect('/seller/orders')->with('success', 'Pesanan berhasil diupdate');
    }

    public function updateStatus(Request $request, $order)
    {
        $request->validate([
            'status' => 'required|in:menunggu,diproses,dikirim,selesai,dibatalkan,pending,processing,shipped,delivered,cancelled',
        ]);

        $order = $this->sellerOrders()->findOrFail($order);
        $status = $this->sellerOrderService->mapOrderStatus($request->status);
        $order->update([
            'status' => $status,
            'shipped_at' => $status === SellerOrder::STATUS_SHIPPED ? ($order->shipped_at ?? now()) : $order->shipped_at,
            'delivered_at' => $status === SellerOrder::STATUS_DELIVERED ? ($order->delivered_at ?? now()) : $order->delivered_at,
        ]);
        $this->sellerOrderService->syncPayout($order->fresh(['payout']));
        $this->sellerOrderService->syncParentOrderStatus($order->order);

        return back()->with('success', 'Status pesanan berhasil diupdate');
    }

    public function updateResi(Request $request, $order)
    {
        $request->validate([
            'resi' => 'required|string|max:255',
            'video_pengiriman' => 'required|file|mimes:mp4,mov,avi|max:20480',
        ]);

        $order = $this->sellerOrders()->findOrFail($order);

        $videoPath = null;
        $videoHash = null;
        if ($request->hasFile('video_pengiriman')) {
            $file = $request->file('video_pengiriman');
            $videoHash = md5_file($file->getRealPath());
            $filename = 'kirim_' . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/videos'), $filename);
            $videoPath = 'assets/videos/' . $filename;
        }

        $order->update([
            'no_resi' => $request->resi,
            'status' => $order->status === SellerOrder::STATUS_DELIVERED ? SellerOrder::STATUS_DELIVERED : SellerOrder::STATUS_SHIPPED,
            'shipped_at' => $order->shipped_at ?? now(),
            'video_pengiriman' => $videoPath,
            'video_pengiriman_hash' => $videoHash,
        ]);
        $this->sellerOrderService->syncPayout($order->fresh(['payout']));
        $this->sellerOrderService->syncParentOrderStatus($order->order);

        return back()->with('success', 'Resi dan video bukti pengiriman berhasil diupdate');
    }

    private function sellerOrders()
    {
        $userId = \Illuminate\Support\Facades\Auth::id();

        return SellerOrder::with(['items' => function ($query) {
                $query->where('type', 'buy')
                    ->with('product');
            }, 'order.buyer', 'store.user', 'seller', 'payout'])
            ->whereHas('items', fn ($query) => $query->where('type', 'buy'))
            ->whereDoesntHave('items', fn ($query) => $query->where('type', 'rent'))
            ->where(function ($query) use ($userId) {
                $query->where('seller_id', $userId)
                    ->orWhereHas('store', fn ($store) => $store->where('user_id', $userId));
            });
    }
}
