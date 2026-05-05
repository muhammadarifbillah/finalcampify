<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;

use App\Models\Pembeli\Wishlist_pembeli;
use Illuminate\Http\Request;

class PembeliWishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist_pembeli::with('product')
            ->where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->get();

        return view('pembeli.wishlist.index_pembeli', compact('wishlists'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $userId = \Illuminate\Support\Facades\Auth::id();
        $productId = $request->product_id;

        $wishlist = Wishlist_pembeli::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
        } else {
            Wishlist_pembeli::create([
                'user_id' => $userId,
                'product_id' => $productId
            ]);
        }

        return back();
    }
}