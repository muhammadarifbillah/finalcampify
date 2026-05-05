<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;

use App\Models\Pembeli\Wishlist_pembeli;
use App\Models\Pembeli\Product_pembeli;

class PembeliDashboardController extends Controller
{
    public function index()
    {
        $produks = Product_pembeli::latest()->take(8)->get()->map(function ($item) {
            $item->type = $item->buy_price ? 'buy' : 'rent';
            return $item;
        });

        $hotItems = Product_pembeli::orderBy('rating', 'desc')->take(3)->get();

        $popularProduks = Product_pembeli::orderBy('rating', 'desc')->take(8)->get()->map(function ($item) {
            $item->type = $item->buy_price ? 'buy' : 'rent';
            return $item;
        });

        $wishlistProduksIds = [];
        if (auth()->check()) {
            $wishlistProduksIds = Wishlist_pembeli::where('user_id', \Illuminate\Support\Facades\Auth::id())
                ->pluck('product_id')
                ->toArray();
        }

        $categories = Product_pembeli::distinct()->pluck('category')->filter()->values();

        return view('pembeli.buyer.dashboard_pembeli', compact('produks', 'popularProduks', 'wishlistProduksIds', 'categories', 'hotItems'));
    }
}
