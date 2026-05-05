<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }

        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (blank($user->status)) {
                $user->forceFill(['status' => 'active'])->save();
            }

            if ($user->role === 'user') {
                $user->forceFill(['role' => 'buyer'])->save();
            }

            if ($user->status !== 'active') {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Akun tidak aktif atau diblokir.',
                ])->onlyInput('email');
            }

            $user->forceFill(['last_login' => now()])->save();
            $request->session()->regenerate();

            return $this->redirectBasedOnRole();
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Show the registration form
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }

        return view('auth.register');
    }

    /**
     * Handle registration
     */
    public function register(StoreUserRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'nama' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $this->normalizeRole($data['role'] ?? 'buyer'),
            'status' => 'active',
        ]);

        Auth::login($user);

        return $this->redirectBasedOnRole();
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Redirect user based on their role
     */
    private function redirectBasedOnRole()
    {
        $user = Auth::user();

        switch ($this->normalizeRole($user->role)) {
            case 'admin':
                return redirect('/admin/dashboard');
            case 'seller':
                return redirect('/seller/dashboard');
            case 'buyer':
            default:
                return redirect('/dashboard');
        }
    }

    private function normalizeRole(?string $role): string
    {
        return match ($role) {
            'admin' => 'admin',
            'seller', 'penjual' => 'seller',
            'buyer', 'pembeli', 'user' => 'buyer',
            default => 'buyer',
        };
    }
}
