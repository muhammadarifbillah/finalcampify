<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payout;
use App\Models\SellerOrder;
use App\Models\SellerOrderItem;
use Illuminate\Support\Collection;

class SellerOrderService
{
    public function syncForOrder(Order $order): Collection
    {
        $order->loadMissing(['details.product.store', 'sellerOrders.items', 'sellerOrders.payout']);

        $groups = $order->details->groupBy(function ($detail) {
            $product = $detail->product;
            $storeId = $product?->store?->id ?? $product?->store_id;
            $sellerId = $product?->store?->user_id ?? $product?->sellerUserId();

            return $storeId ? 'store:' . $storeId : 'seller:' . ($sellerId ?? 'unknown');
        });

        $sellerOrders = collect();

        foreach ($groups as $items) {
            $first = $items->first();
            $product = $first?->product;
            $store = $product?->store;
            $storeId = $store?->id ?? $product?->store_id;
            $sellerId = $store?->user_id ?? $product?->sellerUserId();
            $subtotal = (int) $items->sum(fn ($item) => ((int) $item->harga) * ((int) $item->qty));

            $sellerOrder = SellerOrder::query()
                ->where('order_id', $order->id)
                ->when($storeId, fn ($query) => $query->where('store_id', $storeId), fn ($query) => $query->whereNull('store_id'))
                ->when($sellerId, fn ($query) => $query->where('seller_id', $sellerId), fn ($query) => $query->whereNull('seller_id'))
                ->first();

            if (!$sellerOrder) {
                $sellerOrder = new SellerOrder([
                    'order_id' => $order->id,
                    'store_id' => $storeId,
                    'seller_id' => $sellerId,
                    'status' => $this->mapOrderStatus($order->status),
                    'kurir' => $order->kurir,
                    'no_resi' => $order->no_resi,
                    'video_pengiriman' => $order->video_pengiriman,
                    'video_pengiriman_hash' => $order->video_pengiriman_hash,
                    'shipped_at' => in_array($order->status, ['dikirim', 'selesai'], true) ? $order->updated_at : null,
                    'delivered_at' => $order->status === 'selesai' ? ($order->received_at ?? $order->updated_at) : null,
                ]);
            }

            $sellerOrder->fill([
                'store_id' => $storeId,
                'seller_id' => $sellerId,
                'subtotal' => $subtotal,
            ]);

            $sellerOrder->save();

            if (!$sellerOrder->seller_order_number) {
                $sellerOrder->forceFill([
                    'seller_order_number' => $this->sellerOrderNumber($sellerOrder->id),
                ])->save();
            }

            foreach ($items as $item) {
                SellerOrderItem::updateOrCreate(
                    [
                        'seller_order_id' => $sellerOrder->id,
                        'order_detail_id' => $item->id,
                    ],
                    [
                        'product_id' => $item->product_id,
                        'qty' => (int) $item->qty,
                        'harga' => (int) $item->harga,
                        'type' => $item->type ?? 'buy',
                        'duration' => $item->duration,
                        'start_date' => $item->start_date,
                    ]
                );
            }

            $this->syncPayout($sellerOrder->fresh(['payout']));
            $sellerOrders->push($sellerOrder->fresh(['items.product', 'payout']));
        }

        return $sellerOrders;
    }

    public function markDelivered(SellerOrder $sellerOrder): SellerOrder
    {
        $sellerOrder->forceFill([
            'status' => SellerOrder::STATUS_DELIVERED,
            'delivered_at' => $sellerOrder->delivered_at ?? now(),
        ])->save();

        $this->syncPayout($sellerOrder->fresh(['payout']));
        $this->syncParentOrderStatus($sellerOrder->order);

        return $sellerOrder->fresh(['order', 'items.product', 'payout']);
    }

    public function syncPayout(SellerOrder $sellerOrder): Payout
    {
        $readyAt = $sellerOrder->delivered_at?->copy();
        $existing = $sellerOrder->payout;
        $status = $existing?->status === Payout::STATUS_DISBURSED
            ? Payout::STATUS_DISBURSED
            : $this->payoutStatus($sellerOrder, $readyAt);

        return Payout::updateOrCreate(
            ['seller_order_id' => $sellerOrder->id],
            [
                'amount' => (int) $sellerOrder->subtotal,
                'status' => $status,
                'ready_at' => $readyAt,
                'disbursed_at' => $status === Payout::STATUS_DISBURSED ? $existing?->disbursed_at : null,
            ]
        );
    }

    public function syncParentOrderStatus(?Order $order): void
    {
        if (!$order) {
            return;
        }

        $order->loadMissing('sellerOrders');
        $sellerOrders = $order->sellerOrders;

        if ($sellerOrders->isEmpty()) {
            return;
        }

        $statuses = $sellerOrders->pluck('status');
        $status = 'menunggu';
        $receivedAt = $order->received_at;

        if ($statuses->every(fn ($value) => $value === SellerOrder::STATUS_CANCELLED)) {
            $status = 'dibatalkan';
        } elseif ($statuses->every(fn ($value) => $value === SellerOrder::STATUS_DELIVERED)) {
            $status = 'selesai';
            $receivedAt = $sellerOrders->max('delivered_at') ?? $order->received_at;
        } elseif ($statuses->contains(SellerOrder::STATUS_SHIPPED) || $statuses->contains(SellerOrder::STATUS_DELIVERED)) {
            $status = 'dikirim';
        } elseif ($statuses->contains(SellerOrder::STATUS_PROCESSING)) {
            $status = 'diproses';
        }

        $order->forceFill([
            'status' => $status,
            'received_at' => $status === 'selesai' ? $receivedAt : $order->received_at,
        ])->save();
    }

    public function mapOrderStatus(?string $status): string
    {
        return match ($status) {
            'menunggu', 'pending' => SellerOrder::STATUS_PENDING,
            'diproses', 'processing' => SellerOrder::STATUS_PROCESSING,
            'dikirim', 'shipped' => SellerOrder::STATUS_SHIPPED,
            'selesai', 'delivered' => SellerOrder::STATUS_DELIVERED,
            'dibatalkan', 'cancelled' => SellerOrder::STATUS_CANCELLED,
            default => SellerOrder::STATUS_PROCESSING,
        };
    }

    public function mapSellerStatusToOrderStatus(?string $status): string
    {
        return match ($this->mapOrderStatus($status)) {
            SellerOrder::STATUS_PENDING => 'menunggu',
            SellerOrder::STATUS_PROCESSING => 'diproses',
            SellerOrder::STATUS_SHIPPED => 'dikirim',
            SellerOrder::STATUS_DELIVERED => 'selesai',
            SellerOrder::STATUS_CANCELLED => 'dibatalkan',
            default => 'diproses',
        };
    }

    private function payoutStatus(SellerOrder $sellerOrder, $readyAt): string
    {
        if ($sellerOrder->status !== SellerOrder::STATUS_DELIVERED || !$sellerOrder->delivered_at) {
            return Payout::STATUS_WAITING_DELIVERY;
        }

        return $readyAt && $readyAt->isFuture()
            ? Payout::STATUS_WAITING_HOLD
            : Payout::STATUS_READY_TO_DISBURSE;
    }

    private function sellerOrderNumber(int $id): string
    {
        return 'SO-' . str_pad((string) $id, 3, '0', STR_PAD_LEFT);
    }
}
