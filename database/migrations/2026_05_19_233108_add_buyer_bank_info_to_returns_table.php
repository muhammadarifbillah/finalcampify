<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('returns', function (Blueprint $table) {
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
            $cols = [];
            if (Schema::hasColumn('returns', 'buyer_refund_bank_name')) {
                $cols[] = 'buyer_refund_bank_name';
            }
            if (Schema::hasColumn('returns', 'buyer_refund_bank_account')) {
                $cols[] = 'buyer_refund_bank_account';
            }
            if (Schema::hasColumn('returns', 'buyer_refund_bank_name_owner')) {
                $cols[] = 'buyer_refund_bank_name_owner';
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
