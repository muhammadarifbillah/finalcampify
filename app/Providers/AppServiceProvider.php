<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('layouts.navbar_pembeli', function ($view) {
            if (auth()->check()) {
                $cartCount = \App\Models\Pembeli\Keranjang_pembeli::where('user_id', auth()->id())->count();
                $wishlistCount = \App\Models\Pembeli\Wishlist_pembeli::where('user_id', auth()->id())->count();
                $view->with(['cartCount' => $cartCount, 'wishlistCount' => $wishlistCount]);
            }
        });
    }
}
