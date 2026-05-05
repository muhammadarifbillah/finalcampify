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

        $orders = Order_seller::whereIn('product_id', $productIds)->get();

        $pendingOrders = $orders->where('status', 'pending')->count();

        $totalRevenue = $orders
            ->where('status', 'completed')
            ->sum(function ($order) {
                return $order->qty * ($order->product->harga ?? 0);
            });

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