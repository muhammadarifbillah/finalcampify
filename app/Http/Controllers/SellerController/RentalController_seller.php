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
        $rentals = Rental_seller::with('product')->get();
        return view('SellerView.rentals.index_seller', compact('rentals'));
    }

    public function show($id)
    {
        $rental = Rental_seller::with('product')->findOrFail($id);
        return view('SellerView.rentals.show_seller', compact('rental'));
    }

    public function edit($id)
    {
        $rental = Rental_seller::with('product')->findOrFail($id);
        return view('SellerView.rentals.edit_seller', compact('rental'));
    }

    public function update(Request $request, $id)
    {
        $rental = Rental_seller::findOrFail($id);

        $rental->update([
            'status' => $request->status,
            'catatan' => $request->catatan
        ]);

        return redirect('/seller/rentals')->with('success', 'Penyewaan berhasil diupdate');
    }
}