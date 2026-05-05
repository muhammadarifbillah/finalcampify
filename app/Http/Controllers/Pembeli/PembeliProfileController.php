<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Pembeli\Order_pembeli;
use App\Models\Pembeli\Wishlist_pembeli;

class PembeliProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $tab = $request->query('tab', 'profile');
        $validTabs = ['profile', 'orders', 'address', 'security', 'edit', 'favorites', 'chat'];
        if (!in_array($tab, $validTabs)) {
            $tab = 'profile';
        }

        $orders = Order_pembeli::where('user_id', $user->id)->latest()->get();
        $wishlists = [];

        if ($tab === 'favorites') {
            $wishlists = Wishlist_pembeli::with('product')->where('user_id', $user->id)->get();
        }

        return view('pembeli.profile.index_pembeli', compact('user', 'tab', 'orders', 'wishlists'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only('name', 'email'));

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updateAddress(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'address' => 'required|string|max:1000',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
        ]);

        $user->update($request->only('address', 'city', 'postal_code', 'phone'));

        return back()->with('success', 'Alamat berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password saat ini tidak sesuai.');
        }

        $user->update(['password' => $request->password]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}