<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pembeli\Wishlist_pembeli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist_pembeli::with('product')
            ->where('user_id', Auth::id())
            ->get();

        return response()->json([
            'success' => true,
            'data' => $wishlists
        ]);
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $userId = Auth::id();
        $productId = $request->product_id;

        $wishlist = Wishlist_pembeli::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $status = 'removed';
        } else {
            Wishlist_pembeli::create([
                'user_id' => $userId,
                'product_id' => $productId
            ]);
            $status = 'added';
        }

        return response()->json([
            'success' => true,
            'status' => $status,
            'message' => 'Produk berhasil ' . ($status === 'added' ? 'ditambahkan ke' : 'dihapus dari') . ' wishlist'
        ]);
    }
}
