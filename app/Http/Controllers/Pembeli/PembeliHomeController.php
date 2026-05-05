<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;

use App\Models\Pembeli\Product_pembeli;
use App\Models\Pembeli\Wishlist_pembeli;

class PembeliHomeController extends Controller
{
    public function index()
{
    $produks = Product_pembeli::latest()->take(8)->get()->map(function ($item) {
        $item->type = $item->buy_price ? 'buy' : 'rent';
        return $item;
    });

    $popularProduks = Product_pembeli::orderBy('rating', 'desc')->take(8)->get()->map(function ($item) {
        $item->type = $item->buy_price ? 'buy' : 'rent';
        return $item;
    });

    $wishlistProduksIds = [];
    if (auth()->check()) {
        $wishlistProduksIds = Wishlist_pembeli::where('user_id', auth()->id())
            ->pluck('product_id')
            ->toArray();
    }

    $hotItems = Product_pembeli::orderBy('rating', 'desc')->take(3)->get();
    $categories = Product_pembeli::distinct()->pluck('category')->filter()->values();

    return view('pembeli.buyer.dashboard_pembeli', compact('produks', 'popularProduks', 'wishlistProduksIds', 'categories', 'hotItems'));
}
}
