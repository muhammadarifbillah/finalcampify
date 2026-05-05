<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('couriers')) {
            Schema::create('couriers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('service');
                $table->string('estimate')->nullable();
                $table->unsignedBigInteger('price')->default(0);
                $table->string('status')->default('aktif');
                $table->timestamps();
            });
        }

        if (Schema::hasTable('stores')) {
            Schema::table('stores', function (Blueprint $table) {
                if (!Schema::hasColumn('stores', 'no_telp')) {
                    $table->string('no_telp')->nullable()->after('alamat');
                }
                if (!Schema::hasColumn('stores', 'banner')) {
                    $table->string('banner')->nullable()->after('logo');
                }
            });
        }
    }

    public function down(): void
    {
        //
    }
};
