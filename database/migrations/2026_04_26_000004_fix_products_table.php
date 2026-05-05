<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }
        if (Schema::hasColumn('products', 'id')) {
            return;
        }

        // Disable foreign key checks and drop existing constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        try {
            Schema::table('products', function (Blueprint $table) {
                $table->dropForeign(['store_id']);
            });
        } catch (\Exception $e) {
            // Ignore if constraint doesn't exist
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Schema::rename('products', 'products_old');

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->unsignedBigInteger('price')->default(0);
            $table->string('status')->default('pending');
            $table->boolean('is_rental')->default(false);
            $table->timestamps();
        });

        $oldExists = Schema::hasTable('products_old');
        if ($oldExists) {
            $oldProducts = DB::table('products_old')->get();
            foreach ($oldProducts as $old) {
                DB::table('products')->insert([
                    'store_id' => $old->store_id ?? null,
                    'name' => $old->name,
                    'price' => isset($old->price) ? $old->price : 0,
                    'status' => $old->status ?? 'pending',
                    'is_rental' => $old->is_rental ?? false,
                    'created_at' => $old->created_at ?? now(),
                    'updated_at' => $old->updated_at ?? now(),
                ]);
            }

            Schema::dropIfExists('products_old');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products')) {
            Schema::rename('products', 'products_new');
        }

        if (Schema::hasTable('products_old')) {
            Schema::dropIfExists('products_old');
        }

        Schema::create('products', function (Blueprint $table) {
            $table->string('name');
            $table->unsignedBigInteger('price')->default(0);
            $table->string('status')->default('pending');
            $table->boolean('is_rental')->default(false);
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
        });

        if (Schema::hasTable('products_new')) {
            Schema::dropIfExists('products_new');
        }
    }
};
