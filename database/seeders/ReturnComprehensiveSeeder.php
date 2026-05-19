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
                'reason' => 'Barang sudah sampai tapi warna sedikit pudar dari foto. Seller ramah dan setuju refund sebagian.',
            ],
            [
                'status' => 'rejected',
                'buyer_idx' => 1,
                'seller_idx' => 1,
                'reason' => 'Pembeli mengklaim barang cacat, tapi setelah dicek video unboxing, kerusakan terjadi karena pembukaan paket yang ceroboh.',
            ],
        ];

        foreach ($scenarios as $idx => $s) {

            $buyer = $buyers[$idx % $buyers->count()];
            $seller = $sellers[$idx % $sellers->count()];

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
                'created_at' => now()->subDays(15),
            ]);

            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'qty' => 1,
                'harga' => $product->price,
                'type' => 'buy',
            ]);

            ReturnEscrow::create([
                'order_id' => $order->id,
                'type' => 'jual_beli',
                'status' => $s['status'],
                'escrow_total' => $product->price,
                'deposit_amount' => 0,
                'rental_fee_amount' => 0,
                'total_fines' => 0,
                'deficit' => 0,
                'to_seller' => $s['status'] === 'rejected' ? $product->price : 0,
                'to_buyer' => $s['status'] === 'completed' ? $product->price : 0,
                'expected_date' => now()->subDays(10),
                'created_at' => now()->subDays(12),
            ]);
        }
    }

    private function seedSewaReturns($buyers, $sellers)
    {

        // RANDOM RETURNS
        $otherScenarios = [
            [
                'status' => 'checking',
                'notes' => 'Barang baru sampai, sedang dicek kelengkapannya.'
            ],
            [
                'status' => 'completed',
                'notes' => 'Transaksi selesai, semua barang kembali lengkap.'
            ],
            [
                'status' => 'pending',
                'notes' => 'Penyewa belum update resi pengembalian.'
            ],
        ];

        foreach ($otherScenarios as $idx => $os) {

            $buyer = $buyers[($idx + 1) % $buyers->count()];
            $seller = $sellers[($idx + 1) % $sellers->count()];

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
                'shipping_address' => 'Jl. Kebon Jeruk No. ' . ($idx + 10),
                'metode_pembayaran' => 'transfer',
                'status' => 'selesai',
                'kurir' => 'jne',
                'created_at' => now()->subDays(20),
            ]);

            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'qty' => 1,
                'harga' => $product->price,
                'type' => 'rent',
                'duration' => 2,
                'start_date' => now()->subDays(18),
            ]);

            ReturnEscrow::create([
                'order_id' => $order->id,
                'type' => 'sewa',
                'status' => $os['status'],
                'escrow_total' => $product->price * 2,
                'deposit_amount' => $product->price * 0.5,
                'rental_fee_amount' => $product->price * 1.5,
                'expected_date' => now()->subDays(5),
                'late_fee' => 0,
                'damage_fee' => 0,
                'total_fines' => 0,
                'deficit' => 0,
                'to_seller' => 0,
                'to_buyer' => 0,
                'created_at' => now()->subDays(10),
            ]);
        }
    }
}