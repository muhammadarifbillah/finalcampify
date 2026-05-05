<?php

namespace App\Http\Controllers\SellerController;

use App\Http\Controllers\Controller;
use App\Models\SellerModels\Product_seller;
use App\Models\SellerModels\Order_seller;
use App\Models\SellerModels\StoreRating_seller;
use Illuminate\Support\Facades\Auth;

class DashboardController_seller extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $products = Product_seller::where('user_id', $userId)->get();

        $productIds = $products->pluck('id');

        $orders = Order_seller::with(['details.product'])
            ->whereHas('details', fn ($query) => $query->whereIn('product_id', $productIds))
            ->get();

        $pendingOrders = $orders->whereIn('status', ['menunggu', 'diproses'])->count();

        $totalRevenue = $orders
            ->where('status', 'selesai')
            ->sum('total');

        $avgStoreRating = StoreRating_seller::getAverageRating($userId);
        $storeRatingCount = StoreRating_seller::getRatingCount($userId);

        return view('SellerView.seller.dashboard_seller', compact(
            'products',
            'orders',
            'pendingOrders',
            'totalRevenue',
            'avgStoreRating',
            'storeRatingCount'
        ));
    }
}
