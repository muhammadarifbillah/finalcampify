<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

// Reset semua dulu agar bersih
User::where('role', '!=', 'buyer')->update([
    'ktp_image' => null,
    'ktp_verified_at' => null
]);

// Set KTP hanya untuk pembeli (buyer)
User::where('role', 'buyer')->update([
    'ktp_image' => 'storage/ktp_uploads/sample_ktp.jpg',
    'ktp_verified_at' => null
]);

echo "Berhasil! Foto KTP hanya diterapkan pada role 'buyer'. Role lain dikosongkan.\n";
