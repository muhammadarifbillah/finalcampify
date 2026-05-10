<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Report;
use App\Models\Store;
use App\Models\ReturnEscrow;
use App\Models\Pembeli\ProductRating_pembeli;
use App\Models\Pembeli\StoreRating_pembeli;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UnifiedTransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan data lama agar bersih
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Order::truncate();
        OrderDetail::truncate();
        DB::table('rentals')->truncate();
        ProductRating_pembeli::truncate();
        StoreRating_pembeli::truncate();
        Report::truncate();
        DB::table('returns')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $buyers = User::where('role', 'buyer')->get();
        // Pastikan kita ambil produk yang benar-benar untuk jual (buy_price > 0) dan sewa (rent_price > 0)
        $buyProducts = Product::where('status', 'approved')->where('buy_price', '>', 0)->get();
        $rentProducts = Product::where('status', 'approved')->where('rent_price', '>', 0)->get();

        if ($buyers->isEmpty() || $buyProducts->isEmpty() || $rentProducts->isEmpty()) {
            return;
        }

        foreach ($buyers as $index => $buyer) {
            // Skenario pembeli: 0=Normal, 1=Rusak, 2=Telat, 3=Dispute/Rejected, 4=Success Return
            $scenario = $index % 5;

            // 1. PEMBELIAN (BUY) - Selalu Produk Jual
            $productBuy = $buyProducts->random();
            $orderBuy = Order::create([
                'user_id' => $buyer->id,
                'receiver_name' => $buyer->name,
                'total' => $productBuy->buy_price + 10000,
                'shipping_address' => $buyer->address ?? 'Jl. Merdeka No. 123',
                'shipping_city' => $buyer->city ?? 'Jakarta',
                'shipping_district' => $buyer->district ?? 'Gambir',
                'shipping_postal_code' => $buyer->postal_code ?? '10110',
                'shipping_phone' => $buyer->phone ?? '08123456789',
                'metode_pembayaran' => 'transfer',
                'status' => 'selesai',
                'kurir' => 'jne',
                'no_resi' => 'REG-' . rand(1000, 9999),
                'created_at' => now()->subDays(20),
            ]);

            OrderDetail::create([
                'order_id' => $orderBuy->id,
                'product_id' => $productBuy->id,
                'qty' => 1,
                'harga' => $productBuy->buy_price,
                'type' => 'buy',
            ]);

            // Rating Pembelian
            ProductRating_pembeli::create([
                'user_id' => $buyer->id,
                'order_id' => $orderBuy->id,
                'product_id' => $productBuy->id,
                'rating' => 5,
                'comment' => 'Barang original, sangat memuaskan!',
            ]);

            // Skenario Pengembalian Jual Beli
            if ($scenario == 3) {
                // Rejected Return
                ReturnEscrow::create([
                    'order_id' => $orderBuy->id,
                    'type' => ReturnEscrow::TYPE_JUAL_BELI,
                    'status' => ReturnEscrow::STATUS_REJECTED,
                    'escrow_total' => $productBuy->buy_price,
                    'to_seller' => $productBuy->buy_price,
                    'to_buyer' => 0,
                    'owner_notes' => 'Alasan pengembalian tidak valid, segel sudah dibuka.',
                ]);
            } elseif ($scenario == 4) {
                // Successful Return
                ReturnEscrow::create([
                    'order_id' => $orderBuy->id,
                    'type' => ReturnEscrow::TYPE_JUAL_BELI,
                    'status' => ReturnEscrow::STATUS_COMPLETED,
                    'escrow_total' => $productBuy->buy_price,
                    'to_seller' => 0,
                    'to_buyer' => $productBuy->buy_price,
                    'owner_notes' => 'Barang diterima kembali dengan baik.',
                ]);
            }

            // 2. PENYEWAAN (RENT) - Selalu Produk Sewa
            $productRent = $rentProducts->random();
            $duration = rand(3, 7);
            $totalRent = $productRent->rent_price * $duration;
            $deposit = $productRent->buy_price * 0.25;

            $orderRent = Order::create([
                'user_id' => $buyer->id,
                'receiver_name' => $buyer->name,
                'total' => $totalRent + $deposit + 15000,
                'shipping_address' => $buyer->address ?? 'Jl. Merdeka No. 123',
                'shipping_city' => $buyer->city ?? 'Jakarta',
                'shipping_district' => $buyer->district ?? 'Gambir',
                'shipping_postal_code' => $buyer->postal_code ?? '10110',
                'shipping_phone' => $buyer->phone ?? '08123456789',
                'metode_pembayaran' => 'transfer',
                'status' => 'selesai',
                'kurir' => 'jnt',
                'no_resi' => 'RENT-' . rand(1000, 9999),
                'created_at' => now()->subDays(15),
            ]);

            OrderDetail::create([
                'order_id' => $orderRent->id,
                'product_id' => $productRent->id,
                'qty' => 1,
                'harga' => $productRent->rent_price,
                'type' => 'rent',
                'duration' => $duration,
                'start_date' => now()->subDays(14),
            ]);

            // Sync Rentals Table
            $rentalId = DB::table('rentals')->insertGetId([
                'user_id' => $buyer->id,
                'order_id' => $orderRent->id,
                'product_id' => $productRent->id,
                'start_date' => now()->subDays(14)->toDateString(),
                'end_date' => now()->subDays(14 - $duration)->toDateString(),
                'duration' => $duration,
                'price' => $totalRent,
                'status' => 'selesai',
                'created_at' => now()->subDays(15),
                'updated_at' => now(),
            ]);

            // Rating Penyewaan
            ProductRating_pembeli::create([
                'user_id' => $buyer->id,
                'order_id' => $orderRent->id,
                'product_id' => $productRent->id,
                'rating' => 4,
                'comment' => 'Kualitas alat camping oke, bersih.',
            ]);

            // Skenario Pengembalian Sewa (0=Tepat Waktu, 1=Telat 1 Hari, 2=Telat 3 Hari, 3=Telat 5 Jam, 4=Rusak)
            $expectedDate = now()->subDays(14 - $duration);
            $actualDate = null; // Set null agar dianggap belum dikembalikan (Aktif Terlambat)
            $lateFee = 0;
            $damageFee = 0;
            $status = ReturnEscrow::STATUS_PENDING;
            $notes = 'Menunggu pengembalian barang.';

            if ($scenario == 1) {
                // Late 1 Day (Aktif)
                $notes = 'Penyewa melewati batas waktu 1 hari.';
            } elseif ($scenario == 2) {
                // Late 3 Days (Aktif)
                $notes = 'Penyewa melewati batas waktu 3 hari.';
            } elseif ($scenario == 3) {
                // Late 5 Jam (Aktif)
                $notes = 'Penyewa melewati batas waktu beberapa jam.';
            } elseif ($scenario == 4) {
                // Damaged (Sudah dikembalikan tapi ada masalah)
                $actualDate = $expectedDate->copy();
                $damageFee = $deposit * 0.5;
                $status = ReturnEscrow::STATUS_DISPUTE;
                $notes = 'Barang sudah kembali tapi terdapat kerusakan fisik.';
            } elseif ($scenario == 0) {
                // Tepat Waktu (Selesai)
                $actualDate = $expectedDate->copy();
                $status = ReturnEscrow::STATUS_COMPLETED;
                $notes = 'Selesai tepat waktu.';
            }

            ReturnEscrow::create([
                'order_id' => $orderRent->id,
                'type' => ReturnEscrow::TYPE_SEWA,
                'status' => $status,
                'escrow_total' => $deposit,
                'deposit_amount' => $deposit,
                'rental_fee_amount' => $totalRent,
                'expected_date' => $expectedDate,
                'actual_date' => $actualDate,
                'late_fee' => $lateFee,
                'damage_fee' => $damageFee,
                'total_fines' => $lateFee + $damageFee,
                'to_seller' => $lateFee + $damageFee,
                'to_buyer' => $deposit - ($lateFee + $damageFee),
                'owner_notes' => $notes,
            ]);

            // Store Ratings & Reports
            $sellerId = $productBuy->seller_id ?? $productBuy->user_id;
            if ($sellerId) {
                StoreRating_pembeli::updateOrCreate(
                    ['user_id' => $buyer->id, 'store_id' => $sellerId],
                    ['order_id' => $orderBuy->id, 'rating' => 5, 'comment' => 'Pelayanan sangat baik.']
                );

                if ($scenario == 3) {
                    Report::create([
                        'reporter_id' => $buyer->id,
                        'seller_id' => $sellerId,
                        'store_id' => $productBuy->store_id,
                        'type' => 'store',
                        'reason' => 'Masalah sengketa',
                        'description' => 'Seller tidak kooperatif dalam pengembalian.',
                        'status' => 'pending',
                    ]);
                }
            }
        }

        // Final Sync Product Rating Cache
        foreach (Product::all() as $p) {
            $ratings = ProductRating_pembeli::where('product_id', $p->id)->get();
            if ($ratings->isNotEmpty()) {
                $p->rating = $ratings->avg('rating');
                $p->reviews_count = $ratings->count();
                $p->save();
            }
        }
    }
}
