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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_number')->nullable()->after('id');
            $table->string('video_pengiriman')->nullable()->after('status');
            $table->string('video_pengiriman_hash')->nullable()->after('video_pengiriman');
            $table->boolean('is_disbursed')->default(false)->after('bukti_pembayaran');
            $table->timestamp('disbursed_at')->nullable()->after('is_disbursed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'order_number',
                'video_pengiriman',
                'video_pengiriman_hash',
                'is_disbursed',
                'disbursed_at',
            ]);
        });
    }
};
