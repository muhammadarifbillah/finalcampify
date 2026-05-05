<?php

namespace App\Http\Controllers\SellerController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController_seller extends Controller
{
    public function loginForm()
    {
        return view('SellerView.auth.login_seller');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 🔥 LOGIN ATTEMPT
        if (Auth::attempt($request->only('email', 'password'))) {

            // 🔥 PENTING: regenerate session supaya tidak logout saat request berikutnya
            $request->session()->regenerate();

            $user = Auth::user();

            // redirect berdasarkan role
            if ($user->role == 'admin') {
                return redirect('/admin/dashboard');
            }

            if ($user->role == 'seller') {
                return redirect('/seller/dashboard');
            }

            return redirect('/user/dashboard');
        }

        return back()->with('error', 'Email atau password salah');
    }

    public function registerForm()
    {
        return view('SellerView.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5',
            'role' => 'required|in:user,seller'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password)
        ]);

        return redirect('/login')->with('success', 'Akun berhasil dibuat');
    }

    public function logout()
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login');
    }
}