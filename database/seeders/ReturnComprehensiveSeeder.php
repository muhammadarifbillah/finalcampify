<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ReturnEscrow;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Seeder;

class ReturnComprehensiveSeeder extends Seeder
{
    public function run(): void
    {
        $buyers = User::where('role', 'buyer')->get();

        $sellers = User::where('role', 'seller')
            ->has('store')
            ->with('store')
            ->get();

        if ($buyers->isEmpty() || $sellers->isEmpty()) {
            return;
        }

        $this->seedJualBeliReturns($buyers, $sellers);
        $this->seedSewaReturns($buyers, $sellers);
    }

    private function seedJualBeliReturns($buyers, $sellers)
    {
        $scenarios = [
            [
                'status' => 'completed',
                'buyer_idx' => 0,
                'seller_idx' => 0,
                'reason' => 'Barang sudah sampai tapi warna sedikit pudar dari foto. Seller ramah dan setuju refund.',
            ],
            [
                'status' => 'rejected',
                'buyer_idx' => 1,
                'seller_idx' => 1,
                'reason' => 'Pembeli mengklaim barang cacat, tapi setelah dicek video unboxing, kerusakan terjadi karena pembukaan paket yang ceroboh.',
            ],
            [
                'status' => 'pending',
                'buyer_idx' => 2,
                'seller_idx' => 2,
                'reason' => 'Ukuran sepatu kekecilan, pembeli ingin menukar ukuran atau mengajukan retur dana.',
            ],
            [
                'status' => 'checking',
                'buyer_idx' => 3,
                'seller_idx' => 0,
                'reason' => 'Kemasan luar sobek saat diterima, pembeli mengajukan retur dana untuk kompensasi.',
            ],
            [
                'status' => 'completed',
                'buyer_idx' => 4,
                'seller_idx' => 1,
                'reason' => 'Barang yang dikirim salah tipe, seller menyetujui pengembalian dana penuh.',
            ],
        ];

        foreach ($scenarios as $idx => $s) {
            $buyer = $buyers[$s['buyer_idx'] % $buyers->count()];
            $seller = $sellers[$s['seller_idx'] % $sellers->count()];

            $product = Product::where('store_id', $seller->store->id)
                ->where('jenis_produk', 'jual')
                ->first()
                ?? Product::where('jenis_produk', 'jual')->first();

            if (!$product) {
                continue;
            }

            $order = Order::create([
                'user_id' => $buyer->id,
                'receiver_name' => $buyer->name,
                'total' => $product->price + 15000,
                'shipping_address' => 'Perumahan Indah B-12, ' . $buyer->name,
                'metode_pembayaran' => 'transfer',
                'status' => 'selesai',
                'kurir' => 'jne',
                'created_at' => now()->subDays(15 - $idx),
            ]);

            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'qty' => 1,
                'harga' => $product->price,
                'type' => 'buy',
            ]);

            $isCompleted = $s['status'] === 'completed';
            $isRejected = $s['status'] === 'rejected';

            ReturnEscrow::create([
                'order_id' => $order->id,
                'type' => 'jual_beli',
                'status' => $s['status'],
                'escrow_total' => $product->price,
                'deposit_amount' => 0,
                'rental_fee_amount' => 0,
                'total_fines' => 0,
                'deficit' => 0,
                'to_seller' => $isRejected ? $product->price : 0,
                'to_buyer' => $isCompleted ? $product->price : 0,
                'expected_date' => now()->subDays(10 - $idx),
                'actual_date' => ($isCompleted || $isRejected) ? now()->subDays(5 - $idx) : null,
                'created_at' => now()->subDays(12 - $idx),
            ]);
        }
    }

    private function seedSewaReturns($buyers, $sellers)
    {
        $otherScenarios = [
            [
                'status' => 'checking',
                'buyer_idx' => 1,
                'seller_idx' => 1,
                'expected_days_ago' => 1,
                'created_days_ago' => 5,
                'actual_days_ago' => null,
                'late_fee' => 0,
            ],
            [
                'status' => 'completed',
                'buyer_idx' => 2,
                'seller_idx' => 2,
                'expected_days_ago' => 3,
                'created_days_ago' => 6,
                'actual_days_ago' => 2, // 4 hari durasi penyelesaian (6 - 2)
                'late_fee' => 0,
            ],
            [
                'status' => 'pending',
                'buyer_idx' => 3,
                'seller_idx' => 0,
                'expected_days_ago' => -5, // belum jatuh tempo (5 hari kedepan)
                'created_days_ago' => 2,
                'actual_days_ago' => null,
                'late_fee' => 0,
            ],
            [
                'status' => 'completed',
                'buyer_idx' => 4,
                'seller_idx' => 1,
                'expected_days_ago' => 5,
                'created_days_ago' => 9,
                'actual_days_ago' => 1, // 8 hari durasi penyelesaian (9 - 1)
                'late_fee' => 50000,
            ],
            [
                'status' => 'completed',
                'buyer_idx' => 5,
                'seller_idx' => 2,
                'expected_days_ago' => 7,
                'created_days_ago' => 10,
                'actual_days_ago' => 3, // 7 hari durasi penyelesaian (10 - 3)
                'late_fee' => 75000,
            ],
            [
                'status' => 'pending',
                'buyer_idx' => 6,
                'seller_idx' => 0,
                'expected_days_ago' => 3, // OVERDUE (telat 3 hari)
                'created_days_ago' => 8,
                'actual_days_ago' => null,
                'late_fee' => 0,
            ],
            [
                'status' => 'checking',
                'buyer_idx' => 7,
                'seller_idx' => 1,
                'expected_days_ago' => 4, // OVERDUE (telat 4 hari)
                'created_days_ago' => 9,
                'actual_days_ago' => null,
                'late_fee' => 0,
            ],
        ];

        foreach ($otherScenarios as $idx => $os) {
            $buyer = $buyers[$os['buyer_idx'] % $buyers->count()];
            $seller = $sellers[$os['seller_idx'] % $sellers->count()];

            $product = Product::where('store_id', $seller->store->id)
                ->where('is_rental', true)
                ->first();

            if (!$product) {
                continue;
            }

            $order = Order::create([
                'user_id' => $buyer->id,
                'receiver_name' => $buyer->name,
                'total' => ($product->price * 2) + 10000,
                'shipping_address' => 'Jl. Kebon Jeruk No. ' . ($idx + 10) . ', ' . $buyer->city,
                'metode_pembayaran' => 'transfer',
                'status' => 'selesai',
                'kurir' => 'jne',
                'created_at' => now()->subDays($os['created_days_ago'] + 5),
            ]);

            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'qty' => 1,
                'harga' => $product->price,
                'type' => 'rent',
                'duration' => 2,
                'start_date' => now()->subDays($os['created_days_ago'] + 3),
            ]);

            ReturnEscrow::create([
                'order_id' => $order->id,
                'type' => 'sewa',
                'status' => $os['status'],
                'escrow_total' => $product->price * 2,
                'deposit_amount' => $product->price * 0.5,
                'rental_fee_amount' => $product->price * 1.5,
                'expected_date' => now()->subDays($os['expected_days_ago']),
                'actual_date' => $os['actual_days_ago'] !== null ? now()->subDays($os['actual_days_ago']) : null,
                'late_fee' => $os['late_fee'],
                'damage_fee' => 0,
                'total_fines' => $os['late_fee'],
                'deficit' => 0,
                'to_seller' => $os['status'] === 'completed' ? ($product->price * 1.5) : 0,
                'to_buyer' => $os['status'] === 'completed' ? ($product->price * 0.5 - $os['late_fee']) : 0,
                'created_at' => now()->subDays($os['created_days_ago']),
            ]);
        }
    }
}