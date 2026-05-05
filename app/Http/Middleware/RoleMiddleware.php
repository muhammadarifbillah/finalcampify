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

        if (Auth::user()->role !== $role) {
            // Redirect berdasarkan role user
            switch (Auth::user()->role) {
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
}