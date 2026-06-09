<?php

namespace App\Http\Controllers\SellerController;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Models\SellerModels\Product_seller;
use App\Models\SellerOrder;
use App\Models\SellerModels\StoreRating_seller;
use App\Models\SellerModels\Rental_seller;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;

class DashboardController_seller extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Ambil status toko seller (untuk notifikasi banned/suspended)
        $store = Store::where('user_id', $userId)->first();

        // 1. Ambil Produk Seller
        $products = Product_seller::where('user_id', $userId)->get();
        $productIds = $products->pluck('id');

        // 2. Ambil Data Penyewaan (Rentals)
        $rentals = Rental_seller::whereIn('product_id', $productIds)->get();

        // 3. Ambil Data Pesanan Seller Order per toko/seller
        $orders = SellerOrder::with(['items.product', 'order.buyer', 'store.user', 'payout'])
            ->whereHas('items', fn ($q) => $q->whereIn('product_id', $productIds))
            ->where(function ($query) use ($userId) {
                $query->where('seller_id', $userId)
                    ->orWhereHas('store', fn ($storeQuery) => $storeQuery->where('user_id', $userId));
            })
            ->latest()
            ->get();

        // 4. Hitung Statistik Dasar
        $ordersDone = $orders->where('status', 'delivered');

        $purchaseOrders = $orders->filter(function ($order) {
            return $order->items->contains('type', 'buy')
                && !$order->items->contains('type', 'rent');
        });

        $totalSalesDisbursed = $purchaseOrders
            ->filter(fn ($order) => $order->payout?->status === Payout::STATUS_DISBURSED)
            ->sum(fn ($order) => (int) ($order->payout?->amount ?? $order->subtotal));

        $totalSalesWaitingPayout = $purchaseOrders
            ->filter(fn ($order) => $order->status === SellerOrder::STATUS_DELIVERED
                && $order->payout?->status !== Payout::STATUS_DISBURSED)
            ->sum(fn ($order) => (int) ($order->payout?->amount ?? $order->subtotal));
        
        // Revenue murni (Hanya produk milik seller ini)
        $totalRevenue = $ordersDone->sum(function($o) use ($productIds) {
            return $o->details->whereIn('product_id', $productIds)->sum(function($d) {
                return $d->harga * $d->qty;
            });
        });

        $pendingOrdersCount = $orders->whereIn('status', ['pending', 'processing'])->count();
        
        // Barang rental yang status nya rental aktif (sedang disewa buyer)
        $rentedGearCount = $rentals->where('status', 'active')->count();

        // Permintaan Sewa = Hanya yang perlu konfirmasi (pending)
        $totalRentalRequestsCount = $rentals->where('status', 'pending')->count();

        // 5. Rating Toko/Produk
        $avgStoreRating = StoreRating_seller::getAverageRating($userId);
        $storeRatingCount = StoreRating_seller::getRatingCount($userId);
        $productRatings = \App\Models\SellerModels\ProductRating_seller::whereIn('product_id', $productIds)->get();
        $avgProductRating = $productRatings->avg('rating') ?? 0;
        $qualityScore = round(($avgProductRating / 5) * 100);

        // 6. Sales Chart Data (7 Hari Terakhir) - Termasuk pesanan yang sedang berjalan
        $activeOrders = $orders->whereIn('status', ['processing', 'shipped', 'delivered']);
        $labels = [];
        $dataSales = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = \Carbon\Carbon::now()->subDays($i)->format('d M');
            $dataSales[] = $activeOrders->filter(function($o) use ($date) {
                return \Carbon\Carbon::parse($o->created_at)->format('Y-m-d') == $date;
            })->sum(function($o) use ($productIds) {
                return $o->details->whereIn('product_id', $productIds)->sum(function($d) {
                    return $d->harga * $d->qty;
                });
            });
        }
        $trendUp = count($dataSales) >= 2 ? end($dataSales) >= $dataSales[0] : true;

        // 7. Stock & Chat Score
        $totalStock = $products->sum('stok');
        $stockScore = $totalStock > 0 ? 100 : 0;
        $chatScore = 90; // Placeholder

        // 8. Hitung Dana Penyewaan Selesai & Dana Admin
        $rentalReturns = \Illuminate\Support\Facades\DB::table('returns')
            ->join('rentals', 'returns.rental_id', '=', 'rentals.id')
            ->whereIn('rentals.product_id', $productIds)
            ->where('returns.type', 'sewa')
            ->where('returns.status', 'completed')
            ->select('returns.to_seller', 'returns.rental_fee_amount')
            ->get();

        $completedRentalFunds = $rentalReturns->sum('to_seller');
        $totalAdminFunds = $rentalReturns->sum(fn($r) => $r->rental_fee_amount * 0.1);

        // 9. Hitung Barang Terjual (Buy)
        $soldItemsCount = $ordersDone->sum(function($o) use ($productIds) {
            return $o->details->whereIn('product_id', $productIds)->where('type', 'buy')->sum('qty');
        });

        return view('SellerView.seller.dashboard_seller', compact(
            'store',
            'products',
            'orders',
            'pendingOrdersCount',
            'totalRevenue',
            'avgStoreRating',
            'storeRatingCount',
            'avgProductRating',
            'rentals',
            'rentedGearCount',
            'totalRentalRequestsCount',
            'labels',
            'dataSales',
            'qualityScore',
            'stockScore',
            'chatScore',
            'trendUp',
            'completedRentalFunds',
            'totalAdminFunds',
            'soldItemsCount',
            'totalSalesDisbursed',
            'totalSalesWaitingPayout'
        ));
    }
}
