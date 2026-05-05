<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('category')->nullable()->after('store_id');
            $table->text('description')->nullable()->after('category');
            $table->unsignedBigInteger('buy_price')->default(0)->after('price');
            $table->unsignedBigInteger('rent_price')->default(0)->after('buy_price');
            $table->double('rating', 3, 2)->default(0)->after('is_rental');
            $table->unsignedInteger('reviews_count')->default(0)->after('rating');
            $table->string('image')->nullable()->after('reviews_count');
            $table->unsignedInteger('stock')->default(0)->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'category',
                'description',
                'buy_price',
                'rent_price',
                'rating',
                'reviews_count',
                'image',
                'stock',
            ]);
        });
    }
};
