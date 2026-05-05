<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        if (Auth::user()->status !== 'active') {
            Auth::logout();
            return redirect('/login')->withErrors(['email' => 'Akun tidak aktif atau diblokir.']);
        }

        if ($this->normalizeRole(Auth::user()->role) !== $this->normalizeRole($role)) {
            // Redirect berdasarkan role user
            switch ($this->normalizeRole(Auth::user()->role)) {
                case 'admin':
                    return redirect('/admin/dashboard');
                case 'seller':
                    return redirect('/seller/dashboard');
                case 'buyer':
                    return redirect('/dashboard');
                default:
                    return redirect('/login');
            }
        }

        return $next($request);
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
