<?php

namespace App\Http\Controllers\SellerController;

use App\Http\Controllers\Controller;
use App\Models\SellerModels\Rental_seller;
use App\Models\SellerModels\Product_seller;
use Illuminate\Http\Request;

class RentalController_seller extends Controller
{
    public function index()
    {
        $rentals = $this->sellerRentals()->latest()->get();
        return view('SellerView.rentals.index_seller', compact('rentals'));
    }

    public function show($id)
    {
        $rental = $this->sellerRentals()->findOrFail($id);
        return view('SellerView.rentals.show_seller', compact('rental'));
    }

    public function edit($id)
    {
        $rental = $this->sellerRentals()->findOrFail($id);
        return view('SellerView.rentals.edit_seller', compact('rental'));
    }

    public function update(Request $request, $id)
    {
        $rental = $this->sellerRentals()->findOrFail($id);

        $rental->update([
            'status' => $request->status,
            'catatan' => $request->catatan
        ]);

        return redirect('/seller/rentals')->with('success', 'Penyewaan berhasil diupdate');
    }

    private function sellerRentals()
    {
        return Rental_seller::with(['product', 'user', 'order'])
            ->whereHas('product', function ($query) {
                $query->where('user_id', \Illuminate\Support\Facades\Auth::id())
                    ->orWhereHas('store', fn ($store) => $store->where('user_id', \Illuminate\Support\Facades\Auth::id()));
            });
    }
}