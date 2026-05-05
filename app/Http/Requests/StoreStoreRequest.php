<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // ✅ VERIFIKASI: User hanya bisa membuat 1 toko, dan user-store name harus sesuai
        if (auth()->user()->role === 'admin') {
            return true;
        }

        // User biasa hanya bisa membuat toko untuk dirinya sendiri
        return !auth()->user()->store; // Hanya jika user belum punya toko
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // ✅ ATURAN PENDAFTARAN TOKO
            'nama_toko' => 'required|string|max:100|min:5|unique:stores,nama_toko',
            'deskripsi' => 'nullable|string|max:500|min:20',
            'alamat' => 'required|string|max:255|min:10',
            'logo' => 'nullable|url|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // ✅ PESAN VALIDASI TOKO
            'nama_toko.required' => 'Nama toko harus diisi.',
            'nama_toko.min' => 'Nama toko minimal 5 karakter.',
            'nama_toko.max' => 'Nama toko maksimal 100 karakter.',
            'nama_toko.unique' => 'Nama toko sudah terdaftar. Pilih nama toko yang lain.',
            'deskripsi.min' => 'Deskripsi toko minimal 20 karakter.',
            'deskripsi.max' => 'Deskripsi toko maksimal 500 karakter.',
            'alamat.required' => 'Alamat toko harus diisi.',
            'alamat.min' => 'Alamat minimal 10 karakter.',
            'alamat.max' => 'Alamat maksimal 255 karakter.',
            'logo.url' => 'Logo harus berupa URL yang valid.',
        ];
    }
}
