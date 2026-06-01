<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = ['SO-048', 'SO-049', 'SO-051', 'SO-053', 'SO-054', 'SO-042', 'SO-045'];
foreach ($items as $so) {
    $s = App\Models\SellerOrder::where('seller_order_number', $so)
        ->with(['order.buyer', 'payout'])
        ->first();
    if (!$s) {
        echo "$so: missing\n";
        continue;
    }
    $status = $s->status;
    $deliveredAt = $s->delivered_at ? $s->delivered_at->format('Y-m-d H:i:s') : 'null';
    $receivedAt = $s->order?->received_at ? $s->order->received_at->format('Y-m-d H:i:s') : 'null';
    $payoutStatus = $s->payout?->status ?? 'none';
    $payoutSource = $s->payout?->source ?? 'none';
    echo "$so: status=$status, delivered_at=$deliveredAt, order_received_at=$receivedAt, payout_status=$payoutStatus, payout_source=$payoutSource\n";
}
