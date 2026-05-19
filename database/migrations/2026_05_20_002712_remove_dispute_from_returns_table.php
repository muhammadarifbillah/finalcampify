<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove dispute status from enum and drop dispute_chat_log column.
     */
    public function up(): void
    {
        // 1. Update any existing 'dispute' rows to 'checking' before changing the enum
        DB::table('returns')->where('status', 'dispute')->update(['status' => 'checking']);

        // 2. Alter enum to remove 'dispute' option
        DB::statement("ALTER TABLE `returns` MODIFY COLUMN `status` ENUM('pending', 'checking', 'completed', 'rejected') NOT NULL DEFAULT 'pending'");

        // 3. Drop the dispute_chat_log column if it exists
        if (Schema::hasColumn('returns', 'dispute_chat_log')) {
            Schema::table('returns', function (Blueprint $table) {
                $table->dropColumn('dispute_chat_log');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add dispute to the enum
        DB::statement("ALTER TABLE `returns` MODIFY COLUMN `status` ENUM('pending', 'dispute', 'checking', 'completed', 'rejected') NOT NULL DEFAULT 'pending'");

        // Re-add dispute_chat_log column
        if (!Schema::hasColumn('returns', 'dispute_chat_log')) {
            Schema::table('returns', function (Blueprint $table) {
                $table->json('dispute_chat_log')->nullable()->after('renter_notes');
            });
        }
    }
};
