<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private const HOLDING_PERIOD_DAYS = 7;

    public function up(): void
    {
        if (!Schema::hasTable('seller_orders')) {
            Schema::create('seller_orders', function (Blueprint $table) {
                $table->id();
                $table->string('seller_order_number')->nullable()->unique();
                $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
                $table->foreignId('store_id')->nullable()->constrained('stores')->nullOnDelete();
                $table->foreignId('seller_id')->nullable()->constrained('users')->nullOnDelete();
                $table->unsignedBigInteger('subtotal')->default(0);
                $table->string('status')->default('processing');
                $table->string('kurir')->nullable();
                $table->string('no_resi')->nullable();
                $table->string('video_pengiriman')->nullable();
                $table->string('video_pengiriman_hash')->nullable();
                $table->timestamp('shipped_at')->nullable();
                $table->timestamp('delivered_at')->nullable();
                $table->timestamps();

                $table->index(['order_id', 'store_id']);
                $table->index(['seller_id', 'status']);
                $table->index(['status', 'delivered_at']);
            });
        }

        if (!Schema::hasTable('seller_order_items')) {
            Schema::create('seller_order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('seller_order_id')->constrained('seller_orders')->cascadeOnDelete();
                $table->foreignId('order_detail_id')->nullable()->constrained('order_details')->nullOnDelete();
                $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
                $table->unsignedInteger('qty')->default(1);
                $table->unsignedBigInteger('harga')->default(0);
                $table->string('type')->default('buy');
                $table->unsignedInteger('duration')->nullable();
                $table->date('start_date')->nullable();
                $table->timestamps();

                $table->unique('order_detail_id');
                $table->index(['seller_order_id', 'type']);
            });
        }

        if (!Schema::hasTable('payouts')) {
            Schema::create('payouts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('seller_order_id')->unique()->constrained('seller_orders')->cascadeOnDelete();
                $table->unsignedBigInteger('amount')->default(0);
                $table->string('status')->default('WAITING_DELIVERY');
                $table->timestamp('ready_at')->nullable();
                $table->timestamp('disbursed_at')->nullable();
                $table->timestamps();

                $table->index(['status', 'ready_at']);
            });
        }

        $this->backfillSellerOrders();
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
        Schema::dropIfExists('seller_order_items');
        Schema::dropIfExists('seller_orders');
    }

    private function backfillSellerOrders(): void
    {
        if (!Schema::hasTable('orders') || !Schema::hasTable('order_details')) {
            return;
        }

        if (DB::table('seller_orders')->exists()) {
            return;
        }

        $orders = DB::table('orders')->get()->keyBy('id');
        if ($orders->isEmpty()) {
            return;
        }

        $productHasSellerId = Schema::hasColumn('products', 'seller_id');
        $productHasUserId = Schema::hasColumn('products', 'user_id');
        $storeHasUserId = Schema::hasColumn('stores', 'user_id');

        $select = [
            'order_details.*',
            'products.store_id as product_store_id',
        ];

        if ($productHasSellerId) {
            $select[] = 'products.seller_id as product_seller_id';
            $select[] = DB::raw('NULL as product_user_id');
        } elseif ($productHasUserId) {
            $select[] = DB::raw('NULL as product_seller_id');
            $select[] = 'products.user_id as product_user_id';
        } else {
            $select[] = DB::raw('NULL as product_seller_id');
            $select[] = DB::raw('NULL as product_user_id');
        }

        if ($storeHasUserId) {
            $select[] = 'stores.user_id as store_user_id';
        } else {
            $select[] = DB::raw('NULL as store_user_id');
        }

        $details = DB::table('order_details')
            ->leftJoin('products', 'order_details.product_id', '=', 'products.id')
            ->leftJoin('stores', 'products.store_id', '=', 'stores.id')
            ->select($select)
            ->orderBy('order_details.order_id')
            ->get();

        $groups = $details->groupBy(function ($detail) {
            $storeId = $detail->product_store_id;
            $sellerId = $detail->store_user_id ?? $detail->product_seller_id ?? $detail->product_user_id;
            $ownerKey = $storeId ? 'store:' . $storeId : 'seller:' . ($sellerId ?? 'unknown');

            return $detail->order_id . '|' . $ownerKey;
        });

        foreach ($groups as $items) {
            $first = $items->first();
            $order = $orders->get($first->order_id);

            if (!$order) {
                continue;
            }

            $storeId = $first->product_store_id;
            $sellerId = $first->store_user_id ?? $first->product_seller_id ?? $first->product_user_id;
            $subtotal = (int) $items->sum(fn ($item) => ((int) $item->harga) * ((int) $item->qty));
            $legacyStatus = $order->status ?? 'diproses';
            $status = $this->mapOrderStatus($legacyStatus);
            $deliveredAt = $status === 'delivered'
                ? $this->dateValue($order, 'received_at') ?? $this->dateValue($order, 'updated_at')
                : null;
            $shippedAt = in_array($status, ['shipped', 'delivered'], true)
                ? $this->dateValue($order, 'updated_at')
                : null;

            $sellerOrderId = DB::table('seller_orders')->insertGetId([
                'order_id' => $order->id,
                'store_id' => $storeId,
                'seller_id' => $sellerId,
                'subtotal' => $subtotal,
                'status' => $status,
                'kurir' => $this->value($order, 'kurir'),
                'no_resi' => $this->value($order, 'no_resi'),
                'video_pengiriman' => $this->value($order, 'video_pengiriman'),
                'video_pengiriman_hash' => $this->value($order, 'video_pengiriman_hash'),
                'shipped_at' => $shippedAt,
                'delivered_at' => $deliveredAt,
                'created_at' => $this->dateValue($order, 'created_at') ?? now(),
                'updated_at' => $this->dateValue($order, 'updated_at') ?? now(),
            ]);

            DB::table('seller_orders')
                ->where('id', $sellerOrderId)
                ->update(['seller_order_number' => $this->sellerOrderNumber($sellerOrderId)]);

            foreach ($items as $item) {
                DB::table('seller_order_items')->insert([
                    'seller_order_id' => $sellerOrderId,
                    'order_detail_id' => $item->id,
                    'product_id' => $item->product_id,
                    'qty' => (int) $item->qty,
                    'harga' => (int) $item->harga,
                    'type' => $item->type ?? 'buy',
                    'duration' => $item->duration,
                    'start_date' => $item->start_date,
                    'created_at' => $item->created_at ?? now(),
                    'updated_at' => $item->updated_at ?? now(),
                ]);
            }

            $isDisbursed = (bool) ($this->value($order, 'is_disbursed') ?? false);
            $readyAt = $deliveredAt ? Carbon::parse($deliveredAt)->addDays(self::HOLDING_PERIOD_DAYS) : null;
            $payoutStatus = $this->payoutStatus($status, $readyAt, $isDisbursed);

            DB::table('payouts')->insert([
                'seller_order_id' => $sellerOrderId,
                'amount' => $subtotal,
                'status' => $payoutStatus,
                'ready_at' => $readyAt,
                'disbursed_at' => $isDisbursed ? ($this->dateValue($order, 'disbursed_at') ?? now()) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function mapOrderStatus(?string $status): string
    {
        return match ($status) {
            'menunggu' => 'pending',
            'diproses' => 'processing',
            'dikirim' => 'shipped',
            'selesai' => 'delivered',
            'dibatalkan' => 'cancelled',
            default => in_array($status, ['pending', 'processing', 'shipped', 'delivered', 'cancelled'], true)
                ? $status
                : 'processing',
        };
    }

    private function payoutStatus(string $sellerOrderStatus, ?Carbon $readyAt, bool $isDisbursed): string
    {
        if ($isDisbursed) {
            return 'DISBURSED';
        }

        if ($sellerOrderStatus !== 'delivered' || !$readyAt) {
            return 'WAITING_DELIVERY';
        }

        return $readyAt->isFuture() ? 'WAITING_HOLD' : 'READY_TO_DISBURSE';
    }

    private function sellerOrderNumber(int $id): string
    {
        return 'SO-' . str_pad((string) $id, 3, '0', STR_PAD_LEFT);
    }

    private function value(object $row, string $column): mixed
    {
        return property_exists($row, $column) ? $row->{$column} : null;
    }

    private function dateValue(object $row, string $column): mixed
    {
        $value = $this->value($row, $column);

        return $value ?: null;
    }
};
