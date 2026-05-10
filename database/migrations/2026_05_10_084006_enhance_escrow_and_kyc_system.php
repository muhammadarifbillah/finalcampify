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
        Schema::table('users', function (Blueprint $table) {
            $table->string('ktp_image')->nullable()->after('status');
            $table->timestamp('ktp_verified_at')->nullable()->after('ktp_image');
            $table->boolean('is_fraud')->default(false)->after('ktp_verified_at');
        });

        Schema::table('returns', function (Blueprint $table) {
            $table->decimal('deposit_amount', 15, 2)->default(0)->after('escrow_total');
            $table->decimal('rental_fee_amount', 15, 2)->default(0)->after('deposit_amount');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ktp_image', 'ktp_verified_at', 'is_fraud']);
        });

        Schema::table('returns', function (Blueprint $table) {
            $table->dropColumn(['deposit_amount', 'rental_fee_amount']);
        });
    }
};
