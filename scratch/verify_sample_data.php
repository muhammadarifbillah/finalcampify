<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SellerOrder;
use App\Models\Payout;

$purchaseQuery = SellerOrder::whereHas('items', function ($q) {
    $q->where('type', 'buy');
})->whereDoesntHave('items', function ($q) {
    $q->where('type', 'rent');
});

echo 'purchase-only delivered: ' . $purchaseQuery->where('status', 'delivered')->count() . "\n";
echo 'purchase-only ready payout: ' . Payout::where('status', 'READY_TO_DISBURSE')
    ->whereHas('sellerOrder', function ($q) {
        $q->whereHas('items', function ($q2) {
            $q2->where('type', 'buy');
        })->whereDoesntHave('items', function ($q2) {
            $q2->where('type', 'rent');
        });
    })->count() . "\n";
echo 'purchase-only waiting_hold payout: ' . Payout::where('status', 'WAITING_HOLD')
    ->whereHas('sellerOrder', function ($q) {
        $q->whereHas('items', function ($q2) {
            $q2->where('type', 'buy');
        })->whereDoesntHave('items', function ($q2) {
            $q2->where('type', 'rent');
        });
    })->count() . "\n";
