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

        // Map English status to Indonesian database values if necessary
        $statusMap = [
            'pending' => 'menunggu',
            'processing' => 'diproses',
            'shipped' => 'dikirim',
            'completed' => 'selesai',
            'cancelled' => 'dibatalkan',
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
        ];

        // If status changing to 'dikirim' or already 'dikirim'/'selesai' and a video is uploaded
        if ($request->hasFile('video_pengiriman')) {
            $file = $request->file('video_pengiriman');
            $updateData['video_pengiriman_hash'] = md5_file($file->getRealPath());
            $filename = 'kirim_' . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/videos'), $filename);
            $updateData['video_pengiriman'] = 'assets/videos/' . $filename;
        } else if ($status === 'dikirim' && !$order->video_pengiriman) {
             return back()->with('error', 'Video bukti pengiriman wajib diunggah saat mengubah status menjadi Dikirim.');
        }

        $order->update($updateData);

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
            'status' => $order->status === 'selesai' ? 'selesai' : 'dikirim',
            'video_pengiriman' => $videoPath,
            'video_pengiriman_hash' => $videoHash,
        ]);

        return back()->with('success', 'Resi dan video bukti pengiriman berhasil diupdate');
    }

    private function sellerOrders()
    {
        return Order_seller::with(['details.product', 'product', 'buyer'])
            ->whereHas('details.product', function ($query) {
                $query->where('user_id', \Illuminate\Support\Facades\Auth::id())
                    ->orWhereHas('store', fn ($store) => $store->where('user_id', \Illuminate\Support\Facades\Auth::id()));
            })
            ->whereDoesntHave('details', function ($query) {
                $query->where('type', 'rent');
            });
    }
}
