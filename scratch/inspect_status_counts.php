<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SellerOrder;
use App\Models\Payout;

$statuses = SellerOrder::query()
    ->selectRaw('status, count(*) as total')
    ->groupBy('status')
    ->orderByDesc('total')
    ->get();

foreach ($statuses as $row) {
    echo "SellerOrder status {$row->status}: {$row->total}\n";
}

echo "\nPayout status counts:\n";
$payouts = Payout::query()
    ->selectRaw('status, count(*) as total')
    ->groupBy('status')
    ->orderByDesc('total')
    ->get();

foreach ($payouts as $row) {
    echo "Payout status {$row->status}: {$row->total}\n";
}
