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
            $table->text('address')->nullable()->after('email');
            $table->string('city')->nullable()->after('address');
            $table->string('postal_code')->nullable()->after('city');
            $table->string('phone')->nullable()->after('postal_code');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('receiver_name')->nullable()->after('user_id');
            $table->text('shipping_address')->nullable()->after('total');
            $table->string('shipping_city')->nullable()->after('shipping_address');
            $table->string('shipping_postal_code')->nullable()->after('shipping_city');
            $table->string('shipping_phone')->nullable()->after('shipping_postal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['receiver_name', 'shipping_address', 'shipping_city', 'shipping_postal_code', 'shipping_phone']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['address', 'city', 'postal_code', 'phone']);
        });
    }
};
