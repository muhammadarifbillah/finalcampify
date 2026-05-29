<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->text('klarifikasi_seller')->nullable()->after('alasan_ban');
        });
    }

    public function down()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('klarifikasi_seller');
        });
    }
};
