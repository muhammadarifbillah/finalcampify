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
        // SQLite tidak mendukung menambahkan PRIMARY KEY ke tabel yang sudah ada.
        // Karena migrasi ini dibuat setelah tabel sudah dibuat, kita kosongkan agar tidak gagal.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op untuk tabel couriers.
    }
};
