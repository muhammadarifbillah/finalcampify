<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SellerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->role !== 'seller') {
            abort(403, 'Hanya Seller yang bisa mengakses halaman ini.');
        }

        return $next($request);
    }
}
