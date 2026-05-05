<?php

namespace App\Http\Controllers\SellerController;

use App\Http\Controllers\Controller;
use App\Models\SellerModels\StoreProfile_seller;
use App\Models\SellerModels\Product_seller;
use App\Models\SellerModels\ProductRating_seller;
use App\Models\SellerModels\StoreRating_seller;
use Illuminate\Http\Request;


class StoreProfileController_seller extends Controller
{
    public function index()
    {
        $profile = StoreProfile_seller::where('user_id', auth()->id())->first();
        return view('SellerView.store_profile.index_seller', compact('profile'));
    }

    public function show()
    {
        // Tampilan profil toko (bukan form edit)
        $userId = auth()->id();
        $profile = StoreProfile_seller::where('user_id', $userId)->first();
        
        // Statistik toko
        $products = Product_seller::where('user_id', $userId)->get();
        $totalProducts = $products->count();
        $rentalProducts = $products->where('jenis_produk', 'sewa')->count();
        
        // Rating toko
        $avgRating = StoreRating_seller::getAverageRating($userId);
        $ratingCount = StoreRating_seller::getRatingCount($userId);
        
        return view('SellerView.store_profile.show_seller', compact('profile', 'totalProducts', 'rentalProducts', 'avgRating', 'ratingCount'));
    }

    public function update(Request $request)
    {
        $profile = StoreProfile_seller::where('user_id', auth()->id())->first();

        $data = $request->validate([
            'nama_toko' => 'required',
            'deskripsi' => 'nullable',
            'alamat' => 'nullable',
            'no_telp' => 'nullable',
        ]);

        if ($profile) {
            $profile->update($data);
        } else {
            $data['user_id'] = auth()->id();
            StoreProfile_seller::create($data);
        }

        return redirect('/store-profile/show')->with('success', 'Profil toko berhasil disimpan');
    }
}