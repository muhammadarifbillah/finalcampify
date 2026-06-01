<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SellerOrder;

$sellerOrders = SellerOrder::with('payout')->get();
$counts = [];
foreach ($sellerOrders as $so) {
    $status = $so->payout?->status ?? 'NONE';
    $counts[$status] = ($counts[$status] ?? 0) + 1;
}
foreach ($counts as $status => $count) {
    echo "Payout {$status}: {$count}\n";
}

echo "--- delivered seller orders sample ---\n";
$examples = SellerOrder::where('status', 'delivered')->with('payout', 'order.buyer')->take(10)->get();
foreach ($examples as $s) {
    echo sprintf(
        "%s status=%s deliv=%s payout=%s source=%s buyer=%s\n",
        $s->seller_order_number,
        $s->status,
        $s->delivered_at?->format('Y-m-d') ?? 'null',
        $s->payout?->status ?? 'none',
        $s->payout?->source ?? 'none',
        $s->order?->buyer?->name ?? 'n/a'
    );
}
