<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\ReturnEscrow;

$orderId = 37;
$order = Order::find($orderId);
if (!$order) {
    die("Order #$orderId not found\n");
}

ReturnEscrow::updateOrCreate(
    ['order_id' => $order->id],
    [
        'type' => 'jual_beli',
        'status' => 'checking',
        'escrow_total' => $order->total,
        'renter_notes' => 'Sedang dalam pengecekan unit oleh tim kami.',
        'proof_returned_image' => 'assets/images/checking_mock.jpg'
    ]
);

echo "Success: Mock dispute return created for Order #{$order->id}\n";
