<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SellerOrder;
use App\Models\SellerOrderItem;
use App\Models\Payout;

$totalDelivered = SellerOrder::where('status', 'delivered')->count();
$totalDeliveredPurchase = SellerOrder::whereHas('items', fn($q) => $q->where('type', 'buy'))
    ->whereDoesntHave('items', fn($q) => $q->where('type', 'rent'))
    ->where('status', 'delivered')
    ->count();

$totalReady = Payout::where('status', 'READY_TO_DISBURSE')->count();
$readyPurchase = Payout::where('status', 'READY_TO_DISBURSE')
    ->whereHas('sellerOrder', fn($q) => $q->whereHas('items', fn($q2) => $q2->where('type', 'buy'))
        ->whereDoesntHave('items', fn($q2) => $q2->where('type','rent')))
    ->count();

$totalWaitingDelivery = Payout::where('status', 'WAITING_DELIVERY')->count();
$waitingDeliveryPurchase = Payout::where('status', 'WAITING_DELIVERY')
    ->whereHas('sellerOrder', fn($q) => $q->whereHas('items', fn($q2) => $q2->where('type', 'buy'))
        ->whereDoesntHave('items', fn($q2) => $q2->where('type','rent')))
    ->count();

$totalWithPurchaseItems = SellerOrder::whereHas('items', fn($q) => $q->where('type','buy'))
    ->whereDoesntHave('items', fn($q) => $q->where('type','rent'))
    ->count();

echo "Total delivered: {$totalDelivered}\n";
echo "Total delivered purchase-only: {$totalDeliveredPurchase}\n";
echo "Total READY_TO_DISBURSE payouts: {$totalReady}\n";
echo "Total READY_TO_DISBURSE purchase-only: {$readyPurchase}\n";
echo "Total WAITING_DELIVERY payouts: {$totalWaitingDelivery}\n";
echo "Total WAITING_DELIVERY purchase-only: {$waitingDeliveryPurchase}\n";
echo "Total purchase-only seller orders: {$totalWithPurchaseItems}\n";
