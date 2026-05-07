<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Cek apakah tabel rentals ada
        if (!Schema::hasTable('rentals')) {
            return;
        }

        // Tambahkan kolom price dan duration jika belum ada
        if (!Schema::hasColumn('rentals', 'price')) {
            Schema::table('rentals', function (Blueprint $table) {
                $table->decimal('price', 10, 2)->nullable()->after('total_harga')->comment('Harga per hari');
            });
        }

        if (!Schema::hasColumn('rentals', 'duration')) {
            Schema::table('rentals', function (Blueprint $table) {
                $table->integer('duration')->nullable()->after('price')->comment('Durasi dalam hari');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('rentals')) {
            Schema::table('rentals', function (Blueprint $table) {
                if (Schema::hasColumn('rentals', 'price')) {
                    $table->dropColumn('price');
                }
                if (Schema::hasColumn('rentals', 'duration')) {
                    $table->dropColumn('duration');
                }
            });
        }
    }
};
