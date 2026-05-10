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
            $table->string('proof_sent_image')->nullable()->after('to_buyer');
            $table->string('proof_returned_image')->nullable()->after('proof_sent_image');
            $table->text('owner_notes')->nullable()->after('proof_returned_image');
            $table->text('renter_notes')->nullable()->after('owner_notes');
            $table->json('dispute_chat_log')->nullable()->after('renter_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->dropColumn([
                'proof_sent_image',
                'proof_returned_image',
                'owner_notes',
                'renter_notes',
                'dispute_chat_log'
            ]);
        });
    }
};
