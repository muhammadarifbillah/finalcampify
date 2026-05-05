<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            return;
        }

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('nama_produk')->nullable();
            $table->string('category')->nullable();
            $table->string('kategori')->nullable();
            $table->text('description')->nullable();
            $table->text('deskripsi')->nullable();
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedBigInteger('harga')->default(0);
            $table->unsignedBigInteger('buy_price')->default(0);
            $table->unsignedBigInteger('rent_price')->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('is_rental')->default(false);
            $table->enum('jenis_produk', ['jual', 'sewa'])->default('jual');
            $table->double('rating', 3, 2)->default(0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->string('image')->nullable();
            $table->string('gambar')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('stok')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
