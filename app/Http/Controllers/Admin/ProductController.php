<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('status', 'pending')->get();
        $couriers = Courier::all();

        return view('admin.products', compact('products', 'couriers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'couriers' => 'nullable|array',
            'couriers.*' => 'integer|exists:couriers,id'
        ]);

        $product = Product::create([
            'name' => $data['name'],
            'price' => $data['price'],
            'status' => 'pending',
            'is_rental' => false,
            'store_id' => null,
        ]);

        if (!empty($data['couriers'])) {
            $product->couriers()->sync($data['couriers']);
        }

        return back()->with('success', 'Produk berhasil ditambahkan.');
    }

    public function approve($id)
    {
        Product::findOrFail($id)->update(['status' => 'approved']);
        return back();
    }

    public function reject($id)
    {
        Product::findOrFail($id)->update(['status' => 'rejected']);
        return back();
    }
}