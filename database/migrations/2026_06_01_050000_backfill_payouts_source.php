<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('payouts')) {
            DB::table('payouts')
                ->whereNull('source')
                ->whereNotNull('disbursed_at')
                ->update(['source' => 'auto']);
        }
    }

    public function down(): void
    {
        // no-op
    }
};
