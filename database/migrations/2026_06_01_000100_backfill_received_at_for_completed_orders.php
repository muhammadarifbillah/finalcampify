<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('orders') || !Schema::hasColumn('orders', 'received_at')) {
            return;
        }

        DB::table('orders')
            ->where('status', 'selesai')
            ->whereNull('received_at')
            ->update([
                'received_at' => DB::raw('COALESCE(updated_at, created_at, NOW())'),
            ]);
    }

    public function down(): void
    {
        // Intentionally left blank. Backfilled receipt dates should not be erased on rollback.
    }
};
