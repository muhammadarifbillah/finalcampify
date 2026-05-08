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

        // 3. Ambil Data Pesanan (Orders)
        $orders = Order_seller::with(['details.product'])
            ->whereHas('details', fn ($query) => $query->whereIn('product_id', $productIds))
            ->get();

        // 4. Hitung Statistik Dasar
        $ordersDone = $orders->where('status', 'selesai');
        $totalRevenue = $ordersDone->sum('total');
        $pendingOrdersCount = $orders->whereIn('status', ['menunggu', 'diproses'])->count();
        
        // RE-FILTER: Rented Gear = Hanya yang status 'active'
        $rentedGearCount = $rentals->where('status', 'active')->sum('qty'); 
        // Jika tidak ada kolom qty di rentals, kita gunakan count(). Berdasarkan schema sebelumnya sepertinya rentals adalah per item.
        // Tapi di blade lama ada sum('qty'). Mari kita cek apakah rentals ada qty.
        // Ternyata schema rentals tidak punya qty, tapi detail order punya. 
        // Namun user minta "barang rental yang status nya rental aktif".
        $rentedGearCount = $rentals->where('status', 'active')->count();

        // RE-FILTER: Permintaan Sewa = Hanya yang perlu konfirmasi (pending)
        $totalRentalRequestsCount = $rentals->where('status', 'pending')->count();

        // 5. Rating Toko/Produk
        $productRatings = \App\Models\SellerModels\ProductRating_seller::whereIn('product_id', $productIds)->get();
        $avgProductRating = $productRatings->avg('rating') ?? 0;
        $qualityScore = round(($avgProductRating / 5) * 100);

        // 6. Sales Chart Data (7 Hari Terakhir)
        $labels = [];
        $dataSales = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = \Carbon\Carbon::now()->subDays($i)->format('d M');
            $dataSales[] = $ordersDone->filter(fn($o) => \Carbon\Carbon::parse($o->created_at)->format('Y-m-d') == $date)->sum('total');
        }
        $trendUp = count($dataSales) >= 2 ? end($dataSales) >= $dataSales[0] : true;

        // 7. Stock & Chat Score (Gunakan default atau logic simpel)
        $totalStock = $products->sum('stok');
        $stockScore = $totalStock > 0 ? 100 : 0;
        $chatScore = 90; // Placeholder

        return view('SellerView.seller.dashboard_seller', compact(
            'products',
            'orders',
            'pendingOrdersCount',
            'totalRevenue',
            'avgProductRating',
            'rentals',
            'rentedGearCount',
            'totalRentalRequestsCount',
            'labels',
            'dataSales',
            'qualityScore',
            'stockScore',
            'chatScore',
            'trendUp'
        ));
    }
}
