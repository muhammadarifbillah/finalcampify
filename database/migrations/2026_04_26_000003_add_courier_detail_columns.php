<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('couriers')) {
            Schema::table('couriers', function (Blueprint $table) {
                if (!Schema::hasColumn('couriers', 'estimate')) {
                    $table->string('estimate')->nullable()->after('service');
                }
                if (!Schema::hasColumn('couriers', 'price')) {
                    $table->unsignedBigInteger('price')->default(0)->after('estimate');
                }
                if (!Schema::hasColumn('couriers', 'status')) {
                    $table->string('status')->default('aktif')->after('price');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('couriers')) {
            Schema::table('couriers', function (Blueprint $table) {
                if (Schema::hasColumn('couriers', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('couriers', 'price')) {
                    $table->dropColumn('price');
                }
                if (Schema::hasColumn('couriers', 'estimate')) {
                    $table->dropColumn('estimate');
                }
            });
        }
    }
};
