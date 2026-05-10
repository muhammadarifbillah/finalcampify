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

        $products = Product_seller::where('user_id', $userId)->get();
        $productIds = $products->pluck('id');

        $rental = Rental_seller::whereIn('product_id', $productIds)->get();

        $orders = Order_seller::with(['details.product'])
            ->whereHas('details', fn ($query) => $query->whereIn('product_id', $productIds))
            ->get();

        $avgStoreRating = StoreRating_seller::getAverageRating($userId);
        $storeRatingCount = StoreRating_seller::getRatingCount($userId);

        return view('SellerView.seller.dashboard_seller', compact(
            'products',
            'orders',
            'rental',
            'avgStoreRating',
            'storeRatingCount'
        ));
    }
}
