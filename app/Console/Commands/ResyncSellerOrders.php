<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\SellerOrderService;
use Illuminate\Console\Command;

class ResyncSellerOrders extends Command
{
    protected $signature = 'resync:seller-orders {--limit=0}';
    protected $description = 'Resync SellerOrder records from orders (rebuild delivered_at, status and payouts)';

    public function handle(SellerOrderService $sellerOrderService)
    {
        $limit = (int) $this->option('limit');
        $query = Order::query()->with(['details.product.store']);

        if ($limit > 0) {
            $orders = $query->limit($limit)->get();
        } else {
            $orders = $query->get();
        }

        $this->info('Found ' . $orders->count() . ' orders to process');
        $bar = $this->output->createProgressBar($orders->count());
        $bar->start();

        foreach ($orders as $order) {
            $sellerOrderService->syncForOrder($order);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Resync completed');

        return 0;
    }
}
