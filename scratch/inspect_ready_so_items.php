<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Payout;

$ready = Payout::where('status', 'READY_TO_DISBURSE')->with('sellerOrder.items')->get();
foreach ($ready as $payout) {
    $so = $payout->sellerOrder;
    echo "SO:" . ($so->seller_order_number ?? $so->id) . " status=" . $so->status . " delivered=" . ($so->delivered_at?->format('Y-m-d') ?? 'null') . " items=" . $so->items->pluck('type')->implode(',') . "\n";
}
