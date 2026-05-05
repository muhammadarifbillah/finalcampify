<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Relasi seller / user
            $table->unsignedBigInteger('user_id');

            // Data utama produk
            $table->string('nama_produk');
            $table->text('deskripsi')->nullable();

            // Harga
            $table->integer('harga');

            // Kategori alat outdoor
            // contoh: tenda, carrier, sleeping bag
            $table->string('kategori');

            // Jenis produk
            // jual / sewa
            $table->enum('jenis_produk', ['jual', 'sewa'])->default('jual');

            // Stok barang
            $table->integer('stok')->default(0);

            // Gambar produk
            $table->string('gambar')->nullable();

            $table->timestamps();

            // Foreign Key
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};