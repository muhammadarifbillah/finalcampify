<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change status column from ENUM to VARCHAR safely using raw SQL
        DB::statement("ALTER TABLE returns MODIFY COLUMN status VARCHAR(255) NOT NULL DEFAULT 'pending'");

        Schema::table('returns', function (Blueprint $table) {
            if (!Schema::hasColumn('returns', 'refund_disbursed_at')) {
                $table->timestamp('refund_disbursed_at')->nullable();
            }
            if (!Schema::hasColumn('returns', 'buyer_refund_bank_name')) {
                $table->string('buyer_refund_bank_name')->nullable();
            }
            if (!Schema::hasColumn('returns', 'buyer_refund_bank_account')) {
                $table->string('buyer_refund_bank_account')->nullable();
            }
            if (!Schema::hasColumn('returns', 'buyer_refund_bank_name_owner')) {
                $table->string('buyer_refund_bank_name_owner')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->dropColumn([
                'refund_disbursed_at',
                'buyer_refund_bank_name',
                'buyer_refund_bank_account',
                'buyer_refund_bank_name_owner'
            ]);
        });

        DB::statement("ALTER TABLE returns MODIFY COLUMN status ENUM('pending', 'dispute', 'checking', 'completed', 'rejected') NOT NULL DEFAULT 'pending'");
    }
};
