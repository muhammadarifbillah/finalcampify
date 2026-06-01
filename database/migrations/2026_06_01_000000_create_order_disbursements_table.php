<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('order_disbursements')) {
            return;
        }

        Schema::create('order_disbursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();
            $table->foreignId('seller_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('amount')->default(0);
            $table->string('status')->default('completed');
            $table->string('source')->default('manual');
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('disbursed_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'store_id']);
            $table->index(['seller_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_disbursements');
    }
};
