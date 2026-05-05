<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    public function index()
    {
        return response()->json(Product::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'        => 'required',
            'nama_produk'       => 'required',
            'deskripsi' => 'required',
            'harga'         => 'required|numeric',
            'kategori'      => 'required',
            'image'        => 'requiredrequired|image|mimes:jpg,jpeg,png'
        ]);

        $data = [
            'user_id'      => $request->user_id,
            'nama_produk'     => $request->nama_produk,
            'deskripsi' => $request->deskripsi,
            'harga'       => $request->harga,
            'kategori'    => $request->kategori,
        ];

        if ($request->hasFile('image')) {

            // Simpan gambar baru
            $data['gambar'] = $request->file('image')->store('images', 'public');
        }

        return Product::create($data);
    }

    public function show(Product $product)
    {
        return $product;
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product tidak ditemukan'
            ], 404);
        }

        $data=[
            'nama_produk' => $request->nama_produk,
            'harga'       => $request->harga,
            'kategori'    => $request->kategori,
        ];

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            // Simpan gambar baru
            $data['gambar'] = $request->file('image')->store('images', 'public');
        }

        $product->update($data);

        return response()->json([
            'message' => 'Product berhasil diupdate',
            'data'    => $product
        ], 200);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message'=>'Produk dihapus']);
    }
}
