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
            if (!Schema::hasColumn('stores', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('nama_toko');
            }
            if (!Schema::hasColumn('stores', 'alamat')) {
                $table->text('alamat')->nullable()->after('deskripsi');
            }
            if (!Schema::hasColumn('stores', 'logo')) {
                $table->string('logo')->nullable()->after('alamat');
            }
            if (!Schema::hasColumn('stores', 'no_telp')) {
                $table->string('no_telp')->nullable()->after('alamat');
            }
            if (!Schema::hasColumn('stores', 'banner')) {
                $table->string('banner')->nullable()->after('logo');
            }
            if (!Schema::hasColumn('stores', 'catatan_admin')) {
                $table->text('catatan_admin')->nullable()->after('logo');
            }
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
