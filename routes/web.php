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

/*
|--------------------------------------------------------------------------
| WEB ROUTES CAMPIFY
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/admin/dashboard');
});

Route::prefix('admin')->group(function () {

    // 🔥 DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // 👤 USER
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/delete/{id}', [UserController::class, 'destroy']);

    // 📦 PRODUK (VALIDASI)
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products/store', [ProductController::class, 'store']);
    Route::get('/products/approve/{id}', [ProductController::class, 'approve']);
    Route::get('/products/reject/{id}', [ProductController::class, 'reject']);

    // 🏪 TOKO (BAN)
    Route::get('/stores', [StoreController::class, 'index']);
    Route::get('/stores/ban/{id}', [StoreController::class, 'ban']);
    Route::get('/stores/unban/{id}', [StoreController::class, 'unban']);

    // 📝 ARTIKEL
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::post('/articles/store', [ArticleController::class, 'store']);
    Route::post('/articles/update/{id}', [ArticleController::class, 'update']);
    Route::get('/articles/delete/{id}', [ArticleController::class, 'destroy']);

    // 🚚 KURIR
    Route::get('/couriers', [CourierController::class, 'index']);
    Route::post('/couriers/store', [CourierController::class, 'store']);
    Route::get('/couriers/edit/{id}', [CourierController::class, 'edit']);
    Route::post('/couriers/update/{id}', [CourierController::class, 'update']);
    Route::get('/couriers/delete/{id}', [CourierController::class, 'destroy']);

    // 💬 CHAT
    Route::get('/chats', [ChatController::class, 'index']);
    Route::get('/chats/flag/{id}', [ChatController::class, 'flag']);

    // 🤖 CHATBOT
    Route::get('/chatbot', [ChatbotController::class, 'index']);
    Route::post('/chatbot/store', [ChatbotController::class, 'store']);

    // 📊 MONITORING
    Route::get('/monitoring', [MonitoringController::class, 'index']);

});