<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SellerOrder;
use App\Services\OrderDisbursementService;

$service = app(OrderDisbursementService::class);
$query = SellerOrder::query();
$service->applyStatusFilter($query, 'DITERIMA');
echo 'DITERIMA count: ' . $query->count() . "\n";
$query2 = SellerOrder::query();
$service->applyStatusFilter($query2, 'READY_TO_DISBURSE');
echo 'READY_TO_DISBURSE count: ' . $query2->count() . "\n";
