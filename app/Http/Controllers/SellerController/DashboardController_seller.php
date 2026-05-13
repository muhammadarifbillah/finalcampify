<?php

namespace App\Http\Controllers\SellerController;

use App\Http\Controllers\Controller;
use App\Models\SellerModels\Product_seller;
use App\Models\SellerModels\Order_seller;
use App\Models\SellerModels\StoreRating_seller;
use App\Models\SellerModels\Rental_seller;
use Illuminate\Support\Facades\Auth;

class DashboardController_seller extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 1. Ambil Produk Seller
        $products = Product_seller::where('user_id', $userId)->get();
        $productIds = $products->pluck('id');

        // 2. Ambil Data Penyewaan (Rentals)
        $rentals = Rental_seller::whereIn('product_id', $productIds)->get();

        // 3. Ambil Data Pesanan (Orders) - Filter pesanan yang mengandung produk seller ini
        $orders = Order_seller::with(['details' => function($q) use ($productIds) {
                $q->whereIn('product_id', $productIds)->with('product');
            }, 'buyer', 'rental'])
            ->whereHas('details', fn ($q) => $q->whereIn('product_id', $productIds))
            ->latest()
            ->get();

        // 4. Hitung Statistik Dasar
        $ordersDone = $orders->where('status', 'selesai');
        
        // Revenue murni (Hanya produk milik seller ini)
        $totalRevenue = $ordersDone->sum(function($o) use ($productIds) {
            return $o->details->whereIn('product_id', $productIds)->sum(function($d) {
                return $d->harga * $d->qty;
            });
        });

        $pendingOrdersCount = $orders->whereIn('status', ['menunggu', 'diproses'])->count();
        
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
        $activeOrders = $orders->whereIn('status', ['diproses', 'dikirim', 'selesai']);
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
            'soldItemsCount'
        ));
    }
}
