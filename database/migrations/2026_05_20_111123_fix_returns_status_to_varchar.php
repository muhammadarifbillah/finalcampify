<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Change returns.status from ENUM to VARCHAR(50) to support all return workflow statuses:
     * pending, checking, completed, rejected, approved, refund_pending,
     * replacement_shipping, waiting_refund, denda_pending
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `returns` MODIFY COLUMN `status` VARCHAR(50) NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `returns` MODIFY COLUMN `status` ENUM('pending','checking','completed','rejected') NOT NULL DEFAULT 'pending'");
    }
};
