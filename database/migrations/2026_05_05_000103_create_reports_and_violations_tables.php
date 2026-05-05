<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'flag_reason')) {
                    $table->text('flag_reason')->nullable()->after('status');
                }
                if (!Schema::hasColumn('products', 'reviewed_by')) {
                    $table->foreignId('reviewed_by')->nullable()->after('flag_reason')->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('products', 'reviewed_at')) {
                    $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
                }
            });
        }

        if (!Schema::hasTable('reports')) {
            Schema::create('reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
                $table->string('reason');
                $table->text('description')->nullable();
                $table->enum('status', ['pending', 'reviewed', 'dismissed'])->default('pending');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('violations')) {
            Schema::create('violations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('report_id')->nullable()->constrained('reports')->nullOnDelete();
                $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
                $table->string('source')->default('admin');
                $table->enum('action', ['warning', 'suspend', 'ban'])->default('warning');
                $table->unsignedInteger('strike_count')->default(1);
                $table->text('reason')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('violations');
        Schema::dropIfExists('reports');
    }
};
