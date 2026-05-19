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
            $table->string('buyer_refund_bank_name')->nullable();
            $table->string('buyer_refund_bank_account')->nullable();
            $table->string('buyer_refund_bank_name_owner')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->dropColumn([
                'buyer_refund_bank_name',
                'buyer_refund_bank_account',
                'buyer_refund_bank_name_owner',
            ]);
        });
    }
};
