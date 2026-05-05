<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('product_ratings')) {
            Schema::create('product_ratings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->unsignedTinyInteger('rating');
                $table->text('comment')->nullable();
                $table->text('ulasan')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'product_id', 'order_id']);
            });
        }

        if (!Schema::hasTable('store_ratings')) {
            Schema::create('store_ratings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('store_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->unsignedTinyInteger('rating');
                $table->text('comment')->nullable();
                $table->text('ulasan')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'store_id', 'order_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('store_ratings');
        Schema::dropIfExists('product_ratings');
    }
};
