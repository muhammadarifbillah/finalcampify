<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // ✅ VERIFIKASI: User hanya bisa membuat produk untuk toko mereka sendiri (jika bukan admin)
        if (auth()->user()->role === 'admin') {
            return true;
        }

        $storeId = $this->input('store_id');
        $userStore = auth()->user()->store;

        return $userStore && $userStore->id == $storeId;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|min:3',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:2000|min:10',
            'buy_price' => 'required|numeric|min:1000|max:999999999',
            'rent_price' => 'nullable|numeric|min:0|max:999999999',
            'stock' => 'required|integer|min:0|max:100000',
            'image' => 'nullable|url|max:1000',
            // ✅ VERIFIKASI: store_id HARUS ADA dan valid
            'store_id' => 'required|integer|exists:stores,id',
            'couriers' => 'nullable|array',
            'couriers.*' => 'integer|exists:couriers,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama produk harus diisi.',
            'name.min' => 'Nama produk minimal 3 karakter.',
            'name.max' => 'Nama produk maksimal 255 karakter.',
            'description.min' => 'Deskripsi produk minimal 10 karakter.',
            'description.max' => 'Deskripsi produk maksimal 2000 karakter.',
            'buy_price.required' => 'Harga beli harus diisi.',
            'buy_price.min' => 'Harga beli minimal Rp 1.000.',
            'buy_price.max' => 'Harga beli tidak valid.',
            'stock.required' => 'Stok produk harus diisi.',
            'stock.min' => 'Stok minimal 0.',
            'stock.max' => 'Stok maksimal 100.000.',
            // ✅ VERIFIKASI PESAN UNTUK STORE_ID
            'store_id.required' => 'Toko harus dipilih untuk menambah produk.',
            'store_id.exists' => 'Toko yang dipilih tidak valid atau tidak ditemukan.',
            'couriers.array' => 'Kurir harus dalam format array.',
            'couriers.*.exists' => 'Salah satu kurir tidak valid.',
        ];
    }
}
