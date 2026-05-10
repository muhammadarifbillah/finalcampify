<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pembeli\ProductRating_pembeli;
use App\Models\Product;
use App\Models\Pembeli\StoreRating_pembeli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function storeProductRating(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $existing = ProductRating_pembeli::where('user_id', Auth::id())
            ->where('order_id', $request->order_id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah pernah memberikan ulasan untuk produk ini.',
                'data' => $existing,
            ], 422);
        }

        $rating = ProductRating_pembeli::create([
            'user_id' => Auth::id(),
            'order_id' => $request->order_id,
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // Recalculate product aggregate rating & reviews_count
        $productId = $request->product_id;
        $count = ProductRating_pembeli::where('product_id', $productId)->count();
        $avg = ProductRating_pembeli::where('product_id', $productId)->avg('rating') ?: 0;

        Product::where('id', $productId)->update([
            'rating' => round($avg, 2),
            'reviews_count' => $count,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ulasan produk berhasil disimpan!',
            'data' => $rating,
            'product' => [
                'id' => $productId,
                'rating' => round($avg, 2),
                'reviews_count' => $count,
            ],
        ], 201);
    }

    public function storeStoreRating(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'store_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $user_id = Auth::id();
        
        $existing = StoreRating_pembeli::where('user_id', $user_id)
                                       ->where('store_id', $request->store_id)
                                       ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah pernah memberikan ulasan untuk toko ini.'
            ], 422);
        }

        $rating = StoreRating_pembeli::create([
            'user_id' => $user_id,
            'order_id' => $request->order_id,
            'store_id' => $request->store_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ulasan toko berhasil disimpan!',
            'data' => $rating
        ], 201);
    }

    // Public: list reviews for a product
    public function productReviews($productId)
    {
        $ratings = ProductRating_pembeli::where('product_id', $productId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $ratings->map(function ($r) {
            return [
                'id' => $r->id,
                'user_name' => optional($r->user)->name ?? 'Pengguna',
                'user_avatar' => optional($r->user)->avatar ?? null,
                'rating' => $r->rating,
                'comment' => $r->comment ?? $r->ulasan ?? '',
                'created_at' => $r->created_at->toDateTimeString(),
            ];
        })->toArray();

        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200);
    }
}
