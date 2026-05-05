<?php
namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;

use App\Models\Pembeli\ProductRating_pembeli;
use App\Models\Pembeli\StoreRating_pembeli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembeliReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        ProductRating_pembeli::create([
            'user_id' => Auth::id(),
            'order_id' => $request->order_id,
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Ulasan berhasil disimpan!');
    }

    public function storeRating(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'store_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $user_id = Auth::id();
        
        // Cek apakah user sudah merating toko ini (meski dari pesanan berbeda)
        // Aturan: 1 toko hanya bisa dirating 1 kali oleh user yang sama
        $existing = StoreRating_pembeli::where('user_id', $user_id)
                                       ->where('store_id', $request->store_id)
                                       ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah pernah memberikan ulasan untuk toko ini.');
        }

        StoreRating_pembeli::create([
            'user_id' => $user_id,
            'order_id' => $request->order_id, // Boleh null jika tabel mengizinkan, tapi kita simpan sebagai tracking order mana yang memicu rating
            'store_id' => $request->store_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Ulasan toko berhasil disimpan!');
    }
}
