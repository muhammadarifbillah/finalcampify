<?php
namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;

use App\Models\Pembeli\ProductRating_pembeli;
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
}
