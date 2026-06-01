<?php

namespace App\Console\Commands;

use App\Models\SellerOrder;
use App\Services\OrderDisbursementService;
use Illuminate\Console\Command;
use RuntimeException;
use Illuminate\Support\Facades\Log;

class AutoDisbursePurchaseOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:auto-disburse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically disburse seller orders after delivery hold period';

    /**
     * Execute the console command.
     */
    public function handle(OrderDisbursementService $disbursements)
    {
        $query = SellerOrder::with(['order.buyer', 'store.user', 'seller', 'items.product', 'payout']);
        $disbursements->applyReadyToDisburseFilter($query);
        $sellerOrders = $query->get();

        $disburseCount = 0;
        $skipCount = 0;

        Log::info('AutoDisbursePurchaseOrders: starting auto-disburse run');

        foreach ($sellerOrders as $sellerOrder) {
            $orderLabel = $sellerOrder->seller_order_number ?? $sellerOrder->id;

            try {
                $disbursements->disburse($sellerOrder, null, 'auto');
                Log::info('AutoDisbursePurchaseOrders: auto-disbursed', ['seller_order_id' => $sellerOrder->id, 'seller_order_number' => $orderLabel]);
            } catch (RuntimeException $exception) {
                $this->warn("Skipped Seller Order #{$orderLabel}: {$exception->getMessage()}");
                Log::warning('AutoDisbursePurchaseOrders: skipped seller order', ['seller_order_id' => $sellerOrder->id, 'seller_order_number' => $orderLabel, 'reason' => $exception->getMessage()]);
                $skipCount++;
                continue;
            }

            $this->info("Auto-disbursed Seller Order #{$orderLabel}");
            $disburseCount++;
        }

        $this->line("\n=== Auto-Disburse Summary ===");
        $this->line("Disbursed: {$disburseCount}");
        $this->line("Skipped: {$skipCount}");
        $this->line("Total processed: " . ($disburseCount + $skipCount));

        return self::SUCCESS;
    }
}
