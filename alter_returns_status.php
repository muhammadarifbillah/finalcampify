<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

DB::statement("ALTER TABLE returns MODIFY COLUMN status VARCHAR(255) DEFAULT 'pending'");
echo "Status column altered to VARCHAR(255)\n";
