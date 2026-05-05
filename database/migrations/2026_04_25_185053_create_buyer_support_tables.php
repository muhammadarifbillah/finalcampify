<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('keranjang')) {
            Schema::create('keranjang', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->unsignedInteger('qty')->default(1);
                $table->enum('type', ['buy', 'rent'])->default('buy');
                $table->unsignedInteger('duration')->nullable();
                $table->date('start_date')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('wishlists')) {
            Schema::create('wishlists', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['user_id', 'product_id']);
            });
        }

        if (!Schema::hasTable('rentals')) {
            Schema::create('rentals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->unsignedInteger('duration')->nullable();
                $table->unsignedBigInteger('price')->default(0);
                $table->string('status')->default('active');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('returns')) {
            Schema::create('returns', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rental_id')->constrained('rentals')->cascadeOnDelete();
                $table->string('resi_return');
                $table->string('bukti_denda')->nullable();
                $table->string('kondisi_barang')->default('baik');
                $table->unsignedBigInteger('denda')->default(0);
                $table->timestamp('tanggal_pengembalian')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('returns');
        Schema::dropIfExists('rentals');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('keranjang');
    }
};
