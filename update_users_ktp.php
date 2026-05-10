<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$users = User::all();
foreach ($users as $user) {
    $user->update([
        'ktp_image' => 'storage/ktp_uploads/sample_ktp.jpg',
        'ktp_verified_at' => null, // Set to null so admin can verify
    ]);
}

echo "Semua user berhasil diperbarui dengan foto KTP sample.\n";
