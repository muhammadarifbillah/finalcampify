<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('chats')) {
            Schema::create('chats', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('sender')->nullable();
                $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('receiver_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->text('message');
                $table->string('type')->default('text');
                $table->boolean('is_read')->default(false);
                $table->boolean('is_flagged')->default(false);
                $table->timestamps();
            });

            return;
        }

        Schema::table('chats', function (Blueprint $table) {
            if (!Schema::hasColumn('chats', 'sender')) {
                $table->string('sender')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('chats', 'sender_id')) {
                $table->foreignId('sender_id')->nullable()->after('sender')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('chats', 'receiver_id')) {
                $table->foreignId('receiver_id')->nullable()->after('sender_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('chats', 'order_id')) {
                $table->foreignId('order_id')->nullable()->after('receiver_id')->constrained('orders')->nullOnDelete();
            }
            if (!Schema::hasColumn('chats', 'type')) {
                $table->string('type')->default('text')->after('message');
            }
            if (!Schema::hasColumn('chats', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('type');
            }
        });
    }

    public function down(): void
    {
        //
    }
};
