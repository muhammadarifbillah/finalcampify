<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SellerOrder;
use App\Services\OrderDisbursementService;

$service = app(OrderDisbursementService::class);

$pendingCount = SellerOrder::query()
    ->whereHas('items', fn($q) => $q->where('type', 'buy'))
    ->whereDoesntHave('items', fn($q) => $q->where('type', 'rent'))
    ->where('status', '!=', SellerOrder::STATUS_CANCELLED)
    ->whereDoesntHave('payout', fn($q) => $q->where('status', 'DISBURSED'))
    ->count();

$cancelledCount = SellerOrder::query()->where('status', SellerOrder::STATUS_CANCELLED)->count();

$pendingWithCancelledCount = SellerOrder::query()
    ->whereHas('items', fn($q) => $q->where('type', 'buy'))
    ->whereDoesntHave('items', fn($q) => $q->where('type', 'rent'))
    ->whereDoesntHave('payout', fn($q) => $q->where('status', 'DISBURSED'))
    ->count();

$pendingByService = SellerOrder::query();
$service->applyNotDisbursedFilter($pendingByService);
$pendingByService = $pendingByService->count();

echo "Cancelled seller orders: $cancelledCount\n";
echo "Pending excluding cancelled: $pendingCount\n";
echo "Pending including cancelled in original non-disbursed query: $pendingWithCancelledCount\n";
echo "Pending by service filter: $pendingByService\n";
