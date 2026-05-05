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
        Schema::table('stores', function (Blueprint $table) {
            $table->text('deskripsi')->nullable()->after('nama_toko');
            $table->text('alamat')->nullable()->after('deskripsi');
            $table->string('logo')->nullable()->after('alamat');
            $table->text('catatan_admin')->nullable()->after('logo');
            $table->enum('status', ['pending', 'active', 'rejected', 'suspended', 'banned'])->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['deskripsi', 'alamat', 'logo', 'catatan_admin']);
            $table->enum('status', ['aktif', 'banned', 'nonaktif'])->default('aktif')->change();
        });
    }
};
