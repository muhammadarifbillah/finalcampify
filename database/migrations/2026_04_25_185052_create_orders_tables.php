<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('receiver_name')->nullable();
                $table->unsignedBigInteger('total')->default(0);
                $table->text('shipping_address')->nullable();
                $table->string('shipping_city')->nullable();
                $table->string('shipping_district')->nullable();
                $table->string('shipping_postal_code')->nullable();
                $table->string('shipping_phone')->nullable();
                $table->string('metode_pembayaran')->nullable();
                $table->enum('status', ['menunggu', 'diproses', 'dikirim', 'selesai', 'dibatalkan'])->default('menunggu');
                $table->string('kurir')->nullable();
                $table->string('no_resi')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('order_details')) {
            Schema::create('order_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->unsignedInteger('qty')->default(1);
                $table->unsignedBigInteger('harga')->default(0);
                $table->enum('type', ['buy', 'rent'])->default('buy');
                $table->unsignedInteger('duration')->nullable();
                $table->date('start_date')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_details');
        Schema::dropIfExists('orders');
    }
};
