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
        if (Schema::hasColumn('orders', 'received_at')) {
            return;
        }

        $afterColumn = Schema::hasColumn('orders', 'disbursed_at') ? 'disbursed_at' : 'updated_at';

        Schema::table('orders', function (Blueprint $table) use ($afterColumn) {
            $table->timestamp('received_at')->nullable()->after($afterColumn);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('orders', 'received_at')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('received_at');
        });
    }
};
