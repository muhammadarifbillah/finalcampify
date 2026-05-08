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
        $profile = StoreProfile_seller::where('user_id', \Illuminate\Support\Facades\Auth::id())->first();
        return view('SellerView.store_profile.index_seller', compact('profile'));
    }

    public function show()
    {
        // Tampilan profil toko (bukan form edit)
        $userId = \Illuminate\Support\Facades\Auth::id();
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
        $profile = StoreProfile_seller::where('user_id', \Illuminate\Support\Facades\Auth::id())->first();

        $data = $request->validate([
            'nama_toko' => 'required',
            'deskripsi' => 'nullable',
            'alamat' => 'nullable',
            'no_telp' => 'nullable',
            'bank_name' => 'nullable|string',
            'bank_account_number' => 'nullable|string',
            'bank_account_name' => 'nullable|string',
        ]);

        // Otomatis Geocoding Alamat Toko
        if (!empty($data['alamat'])) {
            try {
                $response = \Illuminate\Support\Facades\Http::withoutVerifying()->withHeaders([
                    'User-Agent' => 'CampifyApp/1.0'
                ])->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $data['alamat'],
                    'format' => 'json',
                    'limit' => 1
                ]);

                if ($response->successful() && isset($response->json()[0])) {
                    $geo = $response->json()[0];
                    $data['latitude'] = $geo['lat'];
                    $data['longitude'] = $geo['lon'];
                }
            } catch (\Exception $e) {
                // Silently fail or log error
            }
        }

        if ($profile) {
            $profile->update($data);
        } else {
            $data['user_id'] = \Illuminate\Support\Facades\Auth::id();
            $data['status'] = 'active';
            StoreProfile_seller::create($data);
        }

        return redirect('/seller/store-profile/show')->with('success', 'Profil toko berhasil disimpan' . (isset($data['latitude']) ? '' : ' (Gagal mendapatkan koordinat otomatis)'));
    }
}
