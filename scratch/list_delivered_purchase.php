<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SellerOrder;

$rows = SellerOrder::whereHas('items', fn($q) => $q->where('type', 'buy'))
    ->whereDoesntHave('items', fn($q) => $q->where('type', 'rent'))
    ->where('status', 'delivered')
    ->with(['order', 'payout'])
    ->get();

foreach ($rows as $s) {
    echo sprintf("SO %s id=%d delivered=%s order_status=%s received=%s payout=%s source=%s\n",
        $s->seller_order_number,
        $s->id,
        $s->delivered_at?->format('Y-m-d') ?? 'null',
        $s->order?->status ?? 'null',
        $s->order?->received_at?->format('Y-m-d') ?? 'null',
        $s->payout?->status ?? 'none',
        $s->payout?->source ?? 'none'
    );
}
