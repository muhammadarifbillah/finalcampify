<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('stores')) {
            return;
        }

        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nama_toko');
            $table->text('deskripsi')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telp')->nullable();
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->enum('status', ['pending', 'active', 'rejected', 'suspended', 'banned'])->default('pending');
            $table->date('last_active')->nullable();
            $table->text('alasan_ban')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
