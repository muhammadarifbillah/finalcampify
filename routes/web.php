<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\CourierController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\ChatbotController;
use App\Http\Controllers\Admin\MonitoringController;

use App\Http\Controllers\Pembeli\PembeliHomeController;
use App\Http\Controllers\Pembeli\PembeliProductController;
use App\Http\Controllers\Pembeli\PembeliCategoryController;
use App\Http\Controllers\Pembeli\PembeliCartController;
use App\Http\Controllers\Pembeli\PembeliWishlistController;
use App\Http\Controllers\Pembeli\PembeliCheckoutController;
use App\Http\Controllers\Pembeli\PembeliProfileController;
use App\Http\Controllers\Pembeli\PembeliOrderController;
use App\Http\Controllers\Pembeli\PembeliChatController;
use App\Http\Controllers\Pembeli\PembeliAuthController;
use App\Http\Controllers\Pembeli\PembeliArticleController;
use App\Http\Controllers\Pembeli\PembeliReviewController;
use App\Http\Controllers\Pembeli\PembeliDashboardController;

use App\Http\Controllers\SellerController\AuthController_seller;
use App\Http\Controllers\SellerController\ProductController_seller;
use App\Http\Controllers\SellerController\DashboardController_seller;
use App\Http\Controllers\SellerController\OrderController_seller;
use App\Http\Controllers\SellerController\RentalController_seller;
use App\Http\Controllers\SellerController\StoreProfileController_seller;
use App\Http\Controllers\SellerController\ChatController_seller;
use App\Http\Controllers\SellerController\RatingController_seller;

/*
|--------------------------------------------------------------------------
| WEB ROUTES CAMPIFY - UNIFIED AUTH SYSTEM
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/login');
});

// 🔥 AUTHENTICATION ROUTES (UNIFIED)
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::get('/register', [App\Http\Controllers\AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// 🔥 ADMIN ROUTES (Role: admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

    // 🔥 DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // 👤 USER
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::get('/users/{id}/activate', [UserController::class, 'activate']);
    Route::get('/users/{id}/deactivate', [UserController::class, 'deactivate']);
    Route::get('/users/{id}/ban', [UserController::class, 'ban']);
    Route::get('/users/delete/{id}', [UserController::class, 'destroy']);

    // 📦 PRODUK (VALIDASI)
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products/store', [ProductController::class, 'store']);
    Route::get('/products/approve/{id}', [ProductController::class, 'approve']);
    Route::get('/products/reject/{id}', [ProductController::class, 'reject']);
    // Tambahan: list semua produk & detail
    Route::get('/products-list', [ProductController::class, 'list'])->name('admin.products.list');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('admin.products.show');

    // 🏪 TOKO (SELLER MANAGEMENT)
    Route::get('/stores', [StoreController::class, 'index'])->name('admin.stores.index');
    Route::get('/stores/{id}', [StoreController::class, 'show'])->name('admin.stores.show');
    Route::post('/stores/{id}/approve', [StoreController::class, 'approve'])->name('admin.stores.approve');
    Route::post('/stores/{id}/reject', [StoreController::class, 'reject'])->name('admin.stores.reject');
    Route::post('/stores/{id}/suspend', [StoreController::class, 'suspend'])->name('admin.stores.suspend');
    Route::post('/stores/{id}/ban', [StoreController::class, 'ban'])->name('admin.stores.ban');
    Route::post('/stores/{id}/activate', [StoreController::class, 'activate'])->name('admin.stores.activate');

    // 📝 ARTIKEL
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::post('/articles/store', [ArticleController::class, 'store']);
    Route::post('/articles/update/{id}', [ArticleController::class, 'update']);
    Route::get('/articles/delete/{id}', [ArticleController::class, 'destroy']);
    Route::get('/articles/show/{id}', [ArticleController::class, 'show']);
    Route::get('/articles/publish/{id}', [ArticleController::class, 'publish']);
    Route::get('/articles/unpublish/{id}', [ArticleController::class, 'unpublish']);

    // 🚚 KURIR (READ-ONLY - Hanya data saja)
    Route::get('/couriers', [CourierController::class, 'index']);

    // 💬 CHAT
    Route::get('/chats', [ChatController::class, 'index']);
    Route::get('/chats/flag/{id}', [ChatController::class, 'flag']);

    // 🤖 CHATBOT
    Route::get('/chatbot', [ChatbotController::class, 'index']);
    Route::post('/chatbot/store', [ChatbotController::class, 'store']);

    // 📊 MONITORING
    Route::get('/monitoring', [MonitoringController::class, 'index']);

});

// 🔥 PUBLIC BUYER ROUTES (No auth required)
Route::get('/home', [PembeliHomeController::class, 'index'])->name('home');

// Product browsing (public)
Route::get('/products/beli', [PembeliProductController::class, 'index'])->name('produk.index');
Route::get('/products/rental', [PembeliProductController::class, 'rentalProducts'])->name('produk.rental');
Route::get('/search', [PembeliProductController::class, 'search'])->name('produk.search');
Route::get('/product/{id}/buy', [PembeliProductController::class, 'detailBuy'])->name('produk.detail.buy');
Route::get('/product/{id}/rent', [PembeliProductController::class, 'detailRent'])->name('produk.detail.rent');
Route::get('/product/{id}', [PembeliProductController::class, 'detail'])->name('produk.detail');
Route::get('/category/{category}', [PembeliProductController::class, 'category'])->name('produk.category');

// Articles (public)
Route::get('/articles', [PembeliArticleController::class, 'index'])->name('articles.index');
Route::get('/article/{id}', [PembeliArticleController::class, 'show'])->name('articles.show');

// Halaman publik artikel untuk user
Route::get('/articles/{id}', [ArticleController::class, 'publicShow'])->name('articles.show.public');

// 🔥 BUYER ROUTES (Role: buyer)
Route::middleware(['auth', 'role:buyer'])->group(function () {
    Route::get('/dashboard', [PembeliDashboardController::class, 'index'])->name('buyer.dashboard');

    // Sewa (protected)
    Route::get('/sewa/form/{id}', [PembeliProductController::class, 'formSewa'])->name('sewa.form');
    Route::post('/sewa/process', [PembeliProductController::class, 'processSewa'])->name('sewa.process');

    // Review (protected)
    Route::post('/review', [PembeliReviewController::class, 'store'])->name('review.store');

    // Cart
    Route::get('/cart', [PembeliCartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [PembeliCartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update/{id}', [PembeliCartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [PembeliCartController::class, 'remove'])->name('cart.remove');

    // Wishlist
    Route::get('/wishlist', [PembeliWishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [PembeliWishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Checkout
    Route::get('/checkout', [PembeliCheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/checkout/{id}', [PembeliCheckoutController::class, 'produk'])->name('checkout.now');
    Route::post('/checkout/process', [PembeliCheckoutController::class, 'process'])->name('checkout.process');

    // User Profile
    Route::get('/profile', [PembeliProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [PembeliProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/address', [PembeliProfileController::class, 'updateAddress'])->name('profile.address.update');
    Route::post('/profile/password', [PembeliProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::get('/orders', [PembeliOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [PembeliOrderController::class, 'detail'])->name('orders.detail');
    Route::get('/orders/return/{detail_id}', [PembeliOrderController::class, 'returnForm'])->name('orders.return');
    Route::post('/orders/return/{detail_id}', [PembeliOrderController::class, 'returnStore'])->name('orders.return.store');
    Route::post('/orders/{id}/cancel', [PembeliOrderController::class, 'cancel'])->name('orders.cancel');

    Route::get('/chat', [PembeliChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [PembeliChatController::class, 'send'])->name('chat.send');
});

// 🔥 SELLER ROUTES (Role: seller)
Route::middleware(['auth', 'role:seller'])->prefix('seller')->group(function () {

    Route::get('/dashboard', [DashboardController_seller::class, 'index'])
        ->name('seller.dashboard');

    // Products
    Route::resource('/products', ProductController_seller::class);

    // Orders
    Route::get('/orders', [OrderController_seller::class, 'index'])->name('seller.orders.index');
    Route::post('/orders/{order}/update-status', [OrderController_seller::class, 'updateStatus'])->name('seller.orders.updateStatus');
    Route::resource('/orders', OrderController_seller::class)->only(['index', 'edit', 'update', 'show']);

    // Rentals
    Route::get('/rentals', [RentalController_seller::class, 'index'])->name('seller.rentals.index');
    Route::get('/rentals/{rental}', [RentalController_seller::class, 'show'])->name('seller.rentals.show');
    Route::get('/rentals/{rental}/edit', [RentalController_seller::class, 'edit'])->name('seller.rentals.edit');
    Route::put('/rentals/{rental}', [RentalController_seller::class, 'update'])->name('seller.rentals.update');

    // Store Profile
    Route::get('/store-profile', [StoreProfileController_seller::class, 'index'])->name('seller.store-profile.index');
    Route::get('/store-profile/show', [StoreProfileController_seller::class, 'show'])->name('seller.store-profile.show');
    Route::post('/store-profile', [StoreProfileController_seller::class, 'update'])->name('seller.store-profile.update');

    // Chat
    Route::get('/chat', [ChatController_seller::class, 'index'])->name('seller.chat.index');
    Route::get('/chat/{userId}', [ChatController_seller::class, 'show'])->name('seller.chat.show');
    Route::post('/chat', [ChatController_seller::class, 'store'])->name('seller.chat.store');

    // Ratings
    Route::get('/ratings', [RatingController_seller::class, 'index'])->name('seller.ratings.index');
    Route::post('/ratings/product', [RatingController_seller::class, 'storeProductRating'])->name('seller.ratings.product');
    Route::post('/ratings/store', [RatingController_seller::class, 'storeStoreRating'])->name('seller.ratings.store');
    Route::get('/ratings/product/{productId}', [RatingController_seller::class, 'getProductRatings'])->name('seller.ratings.product.show');
    Route::get('/ratings/store/{storeId}', [RatingController_seller::class, 'getStoreRatings'])->name('seller.ratings.store.show');
});

/*
|--------------------------------------------------------------------------
| IMAGE STORAGE
|--------------------------------------------------------------------------
*/
Route::get('/images/{path}', function ($path) {
    $file = storage_path('app/public/images/' . $path);

    abort_if(!file_exists($file), 404);

    return response()->file($file, [
        'Access-Control-Allow-Origin' => '*',
    ]);
})->where('path', '.*');
