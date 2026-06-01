<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SellerOrder;

$sellerOrders = SellerOrder::with(['order', 'payout', 'items.product', 'store.user', 'seller'])->limit(20)->get();
foreach ($sellerOrders as $s) {
    echo "SO:" . ($s->seller_order_number ?? $s->id) . "\n";
    echo "  SO status: {$s->status} ({$s->status_label})\n";
    echo "  shipped_at: " . ($s->shipped_at?->format('Y-m-d H:i:s') ?? 'null') . "\n";
    echo "  delivered_at: " . ($s->delivered_at?->format('Y-m-d H:i:s') ?? 'null') . "\n";
    echo "  order status: " . ($s->order?->status ?? 'null') . "\n";
    echo "  order received_at: " . ($s->order?->received_at?->format('Y-m-d H:i:s') ?? 'null') . "\n";
    echo "  payout status: " . ($s->payout?->status ?? 'none') . "\n";
    echo "  payout source: " . ($s->payout?->source ?? 'none') . "\n";
    echo "\n";
}
