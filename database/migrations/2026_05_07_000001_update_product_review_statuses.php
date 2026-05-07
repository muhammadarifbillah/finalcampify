<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('products') || !Schema::hasColumn('products', 'status')) {
            return;
        }

        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE products MODIFY status ENUM('waiting','pending','approved','rejected') NOT NULL DEFAULT 'waiting'");
            DB::table('products')->where('status', 'pending')->update(['status' => 'waiting']);
            DB::statement("ALTER TABLE products MODIFY status ENUM('waiting','approved','rejected') NOT NULL DEFAULT 'waiting'");
            return;
        }

        DB::table('products')->where('status', 'pending')->update(['status' => 'waiting']);
    }

    public function down(): void
    {
        if (!Schema::hasTable('products') || !Schema::hasColumn('products', 'status')) {
            return;
        }

        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE products MODIFY status ENUM('waiting','pending','approved','rejected') NOT NULL DEFAULT 'pending'");
            DB::table('products')->where('status', 'waiting')->update(['status' => 'pending']);
            DB::statement("ALTER TABLE products MODIFY status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");
            return;
        }

        DB::table('products')->where('status', 'waiting')->update(['status' => 'pending']);
    }
};
