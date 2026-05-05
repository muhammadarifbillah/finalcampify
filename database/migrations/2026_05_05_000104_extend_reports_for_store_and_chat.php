<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('reports')) {
            return;
        }

        Schema::table('reports', function (Blueprint $table) {
            if (!Schema::hasColumn('reports', 'type')) {
                $table->string('type')->default('product')->after('id');
            }
            if (!Schema::hasColumn('reports', 'store_id')) {
                $table->foreignId('store_id')->nullable()->after('seller_id')->constrained('stores')->nullOnDelete();
            }
            if (!Schema::hasColumn('reports', 'conversation_id')) {
                $table->foreignId('conversation_id')->nullable()->after('product_id')->constrained('conversations')->nullOnDelete();
            }
            if (!Schema::hasColumn('reports', 'message_id')) {
                $table->foreignId('message_id')->nullable()->after('conversation_id')->constrained('messages')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        //
    }
};
