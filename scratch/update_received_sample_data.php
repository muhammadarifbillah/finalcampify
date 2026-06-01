<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SellerOrder;
use App\Services\SellerOrderService;

$service = app(SellerOrderService::class);

$updates = [
    ['id' => 3, 'delivered_at' => '2026-05-20 09:32:30', 'received_at' => '2026-05-20 09:32:30', 'status' => 'delivered'],
    ['id' => 5, 'delivered_at' => '2026-05-20 09:32:33', 'received_at' => '2026-05-20 09:32:33', 'status' => 'delivered'],
    ['id' => 32, 'delivered_at' => '2026-05-29 09:32:33', 'received_at' => '2026-05-29 09:32:33', 'status' => 'delivered'],
];

foreach ($updates as $update) {
    $sellerOrder = SellerOrder::find($update['id']);
    if (!$sellerOrder) {
        echo "SO id {$update['id']} not found\n";
        continue;
    }

    $sellerOrder->status = $update['status'];
    $sellerOrder->delivered_at = $update['delivered_at'];
    $sellerOrder->shipped_at = $sellerOrder->shipped_at ?? $update['delivered_at'];
    $sellerOrder->save();

    $order = $sellerOrder->order;
    if ($order) {
        $order->status = 'selesai';
        $order->received_at = $update['received_at'];
        $order->save();
    }

    $payout = $service->syncPayout($sellerOrder->fresh(['payout']));

    echo sprintf("Updated SO %s id=%d -> status=delivered, delivered_at=%s, payout=%s\n",
        $sellerOrder->seller_order_number,
        $sellerOrder->id,
        $sellerOrder->delivered_at->format('Y-m-d'),
        $payout->status
    );
}
