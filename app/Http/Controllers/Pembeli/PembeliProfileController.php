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
        $validTabs = ['profile', 'rentals', 'purchases', 'address', 'security', 'edit', 'chat', 'reports'];
        if (!in_array($tab, $validTabs)) {
            $tab = 'profile';
        }

        $allOrders = Order_pembeli::with(['details.product.store', 'returnRequest'])->where('user_id', $user->id)->latest()->get();
        
        // Pesanan yang mengandung setidaknya satu item sewa
        $rentalOrders = $allOrders->filter(function($order) {
            return $order->details->contains(function($d) {
                return $d->type === 'rent' || str_contains(strtolower($d->product->name ?? ''), '(sewa)');
            });
        });
        
        // Pesanan yang mengandung setidaknya satu item beli
        $purchaseOrders = $allOrders->filter(function($order) {
            return $order->details->contains(function($d) {
                return $d->type === 'buy' && !str_contains(strtolower($d->product->name ?? ''), '(sewa)');
            });
        });

        $wishlists = [];
        $reports = [];

        if ($tab === 'favorites') {
            $wishlists = Wishlist_pembeli::with('product')->where('user_id', $user->id)->get();
        }

        if ($tab === 'reports') {
            $reports = \App\Models\Report::with(['product', 'store'])->where('reporter_id', $user->id)->latest()->get();
        }

        return view('pembeli.profile.index_pembeli', compact('user', 'tab', 'rentalOrders', 'purchaseOrders', 'wishlists', 'reports'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'address' => 'required|string|max:1000',
            'city' => 'required|string|max:255',
            'district' => 'nullable|string|max:255',
            'postal_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
        ]);

        $user->update($request->only('name', 'email', 'address', 'city', 'district', 'postal_code', 'phone'));

        return back()->with('success', 'Profil dan alamat berhasil diperbarui.');
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

    public function uploadKtp(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'ktp_image' => 'required|image|max:2048',
        ]);

        if ($request->hasFile('ktp_image')) {
            // Hapus KTP lama jika ada
            if ($user->ktp_image && file_exists(public_path($user->ktp_image))) {
                unlink(public_path($user->ktp_image));
            }

            $file = $request->file('ktp_image');
            $filename = 'ktp_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/images'), $filename);
            
            $user->update([
                'ktp_image' => 'assets/images/' . $filename,
            ]);
            
            return back()->with('success', 'Foto KTP berhasil diunggah. Mohon tunggu verifikasi admin.');
        }

        return back()->with('error', 'Gagal mengunggah foto KTP.');
    }
}