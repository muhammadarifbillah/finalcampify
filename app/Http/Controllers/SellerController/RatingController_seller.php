<?php

namespace App\Http\Controllers\SellerController;

use App\Http\Controllers\Controller;
use App\Models\SellerModels\ProductRating_seller;
use App\Models\SellerModels\StoreRating_seller;
use App\Models\SellerModels\Product_seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController_seller extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | HALAMAN KELOLA RATING SELLER
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $sellerId = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | AMBIL SEMUA PRODUK MILIK SELLER
        |--------------------------------------------------------------------------
        */
        $productIds = Product_seller::where('user_id', $sellerId)->pluck('id');

        /*
        |--------------------------------------------------------------------------
        | REVIEW PRODUK SELLER
        |--------------------------------------------------------------------------
        */
        $productRatings = ProductRating_seller::whereIn('product_id', $productIds)
            ->with(['user:id,name', 'product:id,nama_produk'])
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | REVIEW TOKO SELLER
        |--------------------------------------------------------------------------
        */
        $storeRatings = StoreRating_seller::where('store_id', $sellerId)
            ->with('user:id,name')
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | GABUNG TOTAL REVIEW
        |--------------------------------------------------------------------------
        */
        $totalProductReviews = $productRatings->count();
        $totalStoreReviews = $storeRatings->count();
        $totalReviews = $totalProductReviews + $totalStoreReviews;

        /*
        |--------------------------------------------------------------------------
        | RATING RATA-RATA PRODUK
        |--------------------------------------------------------------------------
        */
        $avgProductRating = $totalProductReviews > 0
            ? round($productRatings->avg('rating'), 1)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | RATING RATA-RATA TOKO
        |--------------------------------------------------------------------------
        */
        $avgStoreRating = $totalStoreReviews > 0
            ? round($storeRatings->avg('rating'), 1)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | RATING SELLER GABUNGAN
        |--------------------------------------------------------------------------
        */
        $averageRating = $totalReviews > 0
            ? round(
                (
                    ($productRatings->sum('rating') + $storeRatings->sum('rating'))
                    / $totalReviews
                ),
                1
            )
            : 0;

        /*
        |--------------------------------------------------------------------------
        | FILTER BINTANG
        |--------------------------------------------------------------------------
        */
        $fiveStar = $productRatings->where('rating', 5)->count() + $storeRatings->where('rating', 5)->count();
        $fourStar = $productRatings->where('rating', 4)->count() + $storeRatings->where('rating', 4)->count();
        $threeStar = $productRatings->where('rating', 3)->count() + $storeRatings->where('rating', 3)->count();
        $twoStar = $productRatings->where('rating', 2)->count() + $storeRatings->where('rating', 2)->count();
        $oneStar = $productRatings->where('rating', 1)->count() + $storeRatings->where('rating', 1)->count();

        /*
        |--------------------------------------------------------------------------
        | KIRIM KE HALAMAN ratings/index.blade.php
        |--------------------------------------------------------------------------
        */
        return view('SellerView.ratings.index_seller', compact(
            'productRatings',
            'storeRatings',
            'averageRating',
            'avgProductRating',
            'avgStoreRating',
            'totalReviews',
            'totalProductReviews',
            'totalStoreReviews',
            'fiveStar',
            'fourStar',
            'threeStar',
            'twoStar',
            'oneStar'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN RATING PRODUK
    |--------------------------------------------------------------------------
    */
    public function storeProductRating(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'ulasan' => 'nullable|string|max:1000',
        ]);

        $userId = Auth::id();

        $existingRating = ProductRating_seller::where('product_id', $request->product_id)
            ->where('user_id', $userId)
            ->first();

        if ($existingRating) {
            $existingRating->update([
                'rating' => $request->rating,
                'ulasan' => $request->ulasan,
            ]);

            return back()->with('success', 'Rating berhasil diperbarui!');
        }

        ProductRating_seller::create([
            'product_id' => $request->product_id,
            'user_id' => $userId,
            'rating' => $request->rating,
            'ulasan' => $request->ulasan,
        ]);

        return back()->with('success', 'Terima kasih atas rating Anda!');
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN RATING TOKO
    |--------------------------------------------------------------------------
    */
    public function storeStoreRating(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'ulasan' => 'nullable|string|max:1000',
        ]);

        $userId = Auth::id();

        $existingRating = StoreRating_seller::where('store_id', $request->store_id)
            ->where('user_id', $userId)
            ->first();

        if ($existingRating) {
            $existingRating->update([
                'rating' => $request->rating,
                'ulasan' => $request->ulasan,
            ]);

            return back()->with('success', 'Rating toko berhasil diperbarui!');
        }

        StoreRating_seller::create([
            'store_id' => $request->store_id,
            'user_id' => $userId,
            'rating' => $request->rating,
            'ulasan' => $request->ulasan,
        ]);

        return back()->with('success', 'Terima kasih atas rating toko Anda!');
    }

    /*
    |--------------------------------------------------------------------------
    | API RATING PRODUK
    |--------------------------------------------------------------------------
    */
    public function getProductRatings($productId)
    {
        $ratings = ProductRating_seller::where('product_id', $productId)
            ->with('user:id,name')
            ->latest()
            ->get();

        $average = ProductRating_seller::getAverageRating($productId);
        $count = ProductRating_seller::getRatingCount($productId);

        return response()->json([
            'ratings' => $ratings,
            'average' => round($average, 1),
            'count' => $count,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | API RATING TOKO
    |--------------------------------------------------------------------------
    */
    public function getStoreRatings($storeId)
    {
        $ratings = StoreRating_seller::where('store_id', $storeId)
            ->with('user:id,name')
            ->latest()
            ->get();

        $average = StoreRating_seller::getAverageRating($storeId);
        $count = StoreRating_seller::getRatingCount($storeId);

        return response()->json([
            'ratings' => $ratings,
            'average' => round($average, 1),
            'count' => $count,
        ]);
    }
}