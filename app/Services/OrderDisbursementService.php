<?php

namespace App\Services;

use App\Models\Payout;
use App\Models\SellerOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class OrderDisbursementService
{
    public const HOLDING_PERIOD_DAYS = 7;

    public function __construct(private SellerOrderService $sellerOrders)
    {
    }

    public function applyPurchaseSellerOrderFilter(Builder $query): Builder
    {
        return $query->whereHas('items', fn ($q) => $q->where('type', 'buy'))
            ->whereDoesntHave('items', fn ($q) => $q->where('type', 'rent'));
    }

    public function applyPurchaseOrderFilter(Builder $query): Builder
    {
        return $this->applyPurchaseSellerOrderFilter($query);
    }

    public function applyNotDisbursedFilter(Builder $query): Builder
    {
        return $query->where('status', '!=', SellerOrder::STATUS_CANCELLED)
            ->whereDoesntHave('payout', function ($query) {
                $query->where('status', Payout::STATUS_DISBURSED);
            });
    }

    public function applyReadyToDisburseFilter(Builder $query): Builder
    {
        $this->applyPurchaseSellerOrderFilter($query);
        $this->applyNotDisbursedFilter($query);

        return $query->where('status', SellerOrder::STATUS_DELIVERED)
            ->whereNotNull('delivered_at');
    }

    public function applyStatusFilter(Builder $query, string $status): Builder
    {
        $this->applyPurchaseSellerOrderFilter($query);

        $normalized = strtoupper($status);

        return match ($normalized) {
            Payout::STATUS_DISBURSED => $query->whereHas('payout', fn ($q) => $q->where('status', Payout::STATUS_DISBURSED)),
            Payout::STATUS_READY_TO_DISBURSE => $this->applyReadyToDisburseOnly($query),
            Payout::STATUS_WAITING_HOLD => $this->applyWaitingHoldOnly($query),
            Payout::STATUS_WAITING_DELIVERY => $this->applyWaitingDeliveryOnly($query),
            'DELIVERED', 'DITERIMA' => $this->applyDeliveredFilter($query),
            default => $this->applyNotDisbursedFilter($query),
        };
    }

    public function eligibility(SellerOrder $sellerOrder): array
    {
        $sellerOrder->loadMissing(['order.buyer', 'store.user', 'seller', 'items.product', 'payout']);
        $payout = $this->sellerOrders->syncPayout($sellerOrder);
        $readyAt = $sellerOrder->delivered_at?->copy();
        $daysUntilReady = 0;

        $statusKey = $payout->status;
        $message = 'Seller order siap dicairkan.';

        if ($payout->status === Payout::STATUS_DISBURSED) {
            $message = 'Dana untuk seller order ini sudah dicairkan.';
        } elseif ($sellerOrder->status !== SellerOrder::STATUS_DELIVERED || !$sellerOrder->delivered_at) {
            $statusKey = Payout::STATUS_WAITING_DELIVERY;
            $message = 'Seller order belum delivered.';
        }

        return [
            'ready' => $statusKey === Payout::STATUS_READY_TO_DISBURSE,
            'status_key' => $statusKey,
            'message' => $message,
            'ready_at' => $readyAt,
            'days_until_ready' => $daysUntilReady,
            'payout' => $payout,
        ];
    }

    public function disburse(SellerOrder $sellerOrder, ?User $actor = null, string $source = 'manual'): SellerOrder
    {
        return DB::transaction(function () use ($sellerOrder, $actor, $source) {
            $lockedSellerOrder = SellerOrder::query()
                ->whereKey($sellerOrder->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $lockedSellerOrder->load(['order.sellerOrders.payout', 'store.user', 'seller', 'items.product', 'payout']);
            $eligibility = $this->eligibility($lockedSellerOrder);

            if (!$eligibility['ready']) {
                throw new RuntimeException($eligibility['message']);
            }

            $disbursedAt = now();

            Payout::updateOrCreate(
                ['seller_order_id' => $lockedSellerOrder->id],
                [
                    'amount' => (int) $lockedSellerOrder->subtotal,
                    'status' => Payout::STATUS_DISBURSED,
                    'source' => $source,
                    'ready_at' => $eligibility['ready_at'],
                    'disbursed_at' => $disbursedAt,
                ]
            );

            $this->syncLegacyOrderDisbursementFlag($lockedSellerOrder);

            Log::info('OrderDisbursementService: disbursed seller order', [
                'seller_order_id' => $lockedSellerOrder->id,
                'seller_order_number' => $lockedSellerOrder->seller_order_number,
                'order_id' => $lockedSellerOrder->order_id,
                'seller_id' => $lockedSellerOrder->seller_id,
                'store_id' => $lockedSellerOrder->store_id,
                'amount' => $lockedSellerOrder->subtotal,
                'source' => $source,
                'disbursed_at' => $disbursedAt->toDateTimeString(),
            ]);

            // Notify seller (user) about disbursement, if possible
            try {
                $notifiable = $lockedSellerOrder->seller ?? $lockedSellerOrder->store?->user;
                if ($notifiable) {
                    $payload = [
                        'seller_order_id' => $lockedSellerOrder->id,
                        'seller_order_number' => $lockedSellerOrder->seller_order_number,
                        'order_id' => $lockedSellerOrder->order_id,
                        'amount' => (int) $lockedSellerOrder->subtotal,
                        'source' => $source,
                        'disbursed_at' => $disbursedAt->toDateTimeString(),
                    ];

                    $notifiable->notify(new \App\Notifications\PayoutDisbursed($payload));
                }
            } catch (\Throwable $e) {
                Log::warning('OrderDisbursementService: failed to notify seller about payout', ['error' => $e->getMessage(), 'seller_order_id' => $lockedSellerOrder->id]);
            }

            return $lockedSellerOrder->fresh(['order.buyer', 'store.user', 'seller', 'items.product', 'payout']);
        });
    }

    public function normalizePayoutStatus(?string $status): ?string
    {
        return match ($status) {
            'waiting-delivery', 'WAITING_DELIVERY' => Payout::STATUS_WAITING_DELIVERY,
            'waiting-hold', 'WAITING_HOLD' => Payout::STATUS_WAITING_HOLD,
            'ready-to-disburse', 'READY_TO_DISBURSE' => Payout::STATUS_READY_TO_DISBURSE,
            'disbursed', 'DISBURSED' => Payout::STATUS_DISBURSED,
            'pending', null, '' => null,
            default => null,
        };
    }

    private function applyReadyToDisburseOnly(Builder $query): Builder
    {
        $this->applyNotDisbursedFilter($query);

        return $query->where('status', SellerOrder::STATUS_DELIVERED)
            ->whereNotNull('delivered_at');
    }

    private function applyWaitingHoldOnly(Builder $query): Builder
    {
        $this->applyNotDisbursedFilter($query);

        return $query->where('status', SellerOrder::STATUS_DELIVERED)
            ->whereNotNull('delivered_at');
    }

    private function applyWaitingDeliveryOnly(Builder $query): Builder
    {
        $this->applyNotDisbursedFilter($query);

        return $query->where(function ($query) {
            $query->where('status', '!=', SellerOrder::STATUS_DELIVERED)
                ->orWhereNull('delivered_at');
        });
    }

    public function applyDeliveredFilter(Builder $query): Builder
    {
        $this->applyPurchaseSellerOrderFilter($query);

        return $query->where('status', SellerOrder::STATUS_DELIVERED)
            ->whereNotNull('delivered_at');
    }

    private function syncLegacyOrderDisbursementFlag(SellerOrder $sellerOrder): void
    {
        $order = $sellerOrder->order;

        if (!$order) {
            return;
        }

        $order->load('sellerOrders.payout');
        $allDisbursed = $order->sellerOrders->isNotEmpty()
            && $order->sellerOrders->every(fn ($item) => $item->payout?->status === Payout::STATUS_DISBURSED);

        if ($allDisbursed) {
            $order->forceFill([
                'is_disbursed' => true,
                'disbursed_at' => now(),
            ])->save();
        }
    }
}
