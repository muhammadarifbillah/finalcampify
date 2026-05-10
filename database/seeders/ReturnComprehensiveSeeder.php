<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ReturnEscrow;
use App\Models\Store;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ReturnComprehensiveSeeder extends Seeder
{
    public function run(): void
    {
        $buyers = User::where('role', 'buyer')->get();
        $sellers = User::where('role', 'seller')->has('store')->with('store')->get();

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
            [
                'status' => 'dispute',
                'buyer_idx' => 2,
                'seller_idx' => 2,
                'reason' => 'Paket diterima dalam keadaan penyok parah dan isi produk (kompor) tidak bisa menyala.',
            ],
        ];

        foreach ($scenarios as $idx => $s) {
            $buyer = $buyers[$idx % $buyers->count()];
            $seller = $sellers[$idx % $sellers->count()];
            $product = Product::where('store_id', $seller->store->id)->where('jenis_produk', 'jual')->first() 
                      ?? Product::where('jenis_produk', 'jual')->first();

            if (!$product) continue;

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
                'expected_date' => now()->subDays(10),
                'created_at' => now()->subDays(12),
            ]);
        }
    }

    private function seedSewaReturns($buyers, $sellers)
    {
        // REALISTIC DISPUTE SCENARIO
        $buyer = $buyers->where('email', 'agus.pratama@gmail.com')->first() ?? $buyers->first();
        $seller = $sellers->where('email', 'slamet.outdoor@gmail.com')->first() ?? $sellers->first();
        $product = Product::where('store_id', $seller->store->id)->where('is_rental', true)->first();

        if ($product) {
            $order = Order::create([
                'user_id' => $buyer->id,
                'receiver_name' => $buyer->name,
                'total' => ($product->price * 3) + 20000,
                'shipping_address' => 'Apartemen Maple Tower A, Jakarta',
                'metode_pembayaran' => 'ewallet',
                'status' => 'selesai',
                'kurir' => 'gosend',
                'created_at' => now()->subDays(10),
            ]);

            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'qty' => 1,
                'harga' => $product->price,
                'type' => 'rent',
                'duration' => 3,
                'start_date' => now()->subDays(8),
            ]);

            $chatLog = [
                [
                    'sender' => 'owner',
                    'name' => $seller->store->nama_toko,
                    'message' => 'Halo Kak ' . $buyer->name . ', barang sudah kami terima kembali. Tapi setelah dicek, ada sobekan di bagian flysheet tendanya ya. Mohon penjelasannya.',
                    'time' => '14:20'
                ],
                [
                    'sender' => 'renter',
                    'name' => $buyer->name,
                    'message' => 'Waduh maaf banget kak, kemarin pas badai di pos 4 kena ranting pohon. Saya nggak sengaja kak.',
                    'time' => '14:35'
                ],
                [
                    'sender' => 'owner',
                    'name' => $seller->store->nama_toko,
                    'message' => 'Wah, kalau sobek gitu fungsinya jadi berkurang kak, apalagi kalau hujan lagi. Kami harus servis atau ganti bagian itu. Estimasi biaya servis sekitar 150rb kak.',
                    'time' => '14:45'
                ],
                [
                    'sender' => 'renter',
                    'name' => $buyer->name,
                    'message' => 'Boleh kak, saya bersedia tanggung jawab. Potong dari dana jaminan saja ya. Terima kasih pengertiannya.',
                    'time' => '15:00'
                ]
            ];

            $rentPrice = $product->price * 2;
            $deposit = 50000; // Tambahan Dana Jaminan/Deposit

            ReturnEscrow::create([
                'order_id' => $order->id,
                'type' => 'sewa',
                'status' => 'dispute',
                'escrow_total' => $rentPrice + $deposit, // Total = Harga Sewa + Deposit
                'expected_date' => now()->subDays(3),
                'actual_date' => now()->subDays(2),
                'late_fee' => 0,
                'damage_fee' => 0,
                'dispute_chat_log' => $chatLog,
                'proof_sent_image' => 'https://images.unsplash.com/photo-1504280390224-dd9e2f9d6ab2?w=800&q=80',
                'proof_returned_image' => 'https://images.unsplash.com/photo-1504280390224-dd9e2f9d6ab2?w=800&q=80',
                'created_at' => now()->subDays(5),
            ]);

            // Create real conversation for buyer & seller visibility
            $conv = Conversation::firstOrCreate([
                'buyer_id' => $buyer->id,
                'seller_id' => $seller->id,
                'product_id' => $product->id,
            ]);

            foreach ($chatLog as $log) {
                Message::create([
                    'conversation_id' => $conv->id,
                    'sender_id' => ($log['sender'] === 'renter' ? $buyer->id : $seller->id),
                    'message' => $log['message'],
                    'created_at' => now()->subDays(1), // slightly in the past
                ]);
            }
        }

        // SCENARIO 2: SITI AMINAH VS RIMBA ADVENTURE (KOMPOR RUSAK)
        $buyer2 = $buyers->where('email', 'siti.aminah@yahoo.com')->first();
        $seller2 = $sellers->where('email', 'ahmad.rimba@gmail.com')->first();
        
        if ($buyer2 && $seller2 && $seller2->store) {
            $product2 = Product::where('store_id', $seller2->store->id)->where('is_rental', true)->first();

            if ($product2) {
            $order2 = Order::create([
                'user_id' => $buyer2->id,
                'receiver_name' => $buyer2->name,
                'total' => ($product2->price * 2) + 15000,
                'shipping_address' => 'Jl. Kebon Jeruk No. 45, Jakarta Barat',
                'metode_pembayaran' => 'transfer',
                'status' => 'selesai',
                'kurir' => 'jne',
                'created_at' => now()->subDays(12),
            ]);

            OrderDetail::create([
                'order_id' => $order2->id,
                'product_id' => $product2->id,
                'qty' => 1,
                'harga' => $product2->price,
                'type' => 'rent',
                'duration' => 2,
                'start_date' => now()->subDays(10),
            ]);

            $chatLog2 = [
                [
                    'sender' => 'owner',
                    'name' => $seller2->store->nama_toko,
                    'message' => 'Malam kak Siti, ini kompornya pas kami coba nyalain kok pemantiknya macet ya? Kemarin pas dikirim lancar jaya.',
                    'time' => '19:00'
                ],
                [
                    'sender' => 'renter',
                    'name' => $buyer2->name,
                    'message' => 'Loh iya kah kak? Saya pakai normal kok kemarin buat masak air. Mungkin kena tumpahan kuah sup dikit kak, tapi harusnya nggak sampai macet.',
                    'time' => '19:15'
                ],
                [
                    'sender' => 'owner',
                    'name' => $seller2->store->nama_toko,
                    'message' => 'Ini kalau macet harus dibongkar kak, kena biaya servis 50rb di tukang servis langganan kami.',
                    'time' => '19:30'
                ]
            ];

            $rentPrice2 = $product2->price * 2;
            $deposit2 = 100000; // Dana Jaminan Kompor

            ReturnEscrow::create([
                'order_id' => $order2->id,
                'type' => 'sewa',
                'status' => 'dispute',
                'escrow_total' => $rentPrice2 + $deposit2,
                'expected_date' => now()->subDays(7),
                'actual_date' => now()->subDays(7), // returned on time but damaged
                'late_fee' => 0,
                'damage_fee' => 0,
                'dispute_chat_log' => $chatLog2,
                'proof_sent_image' => 'https://images.unsplash.com/photo-1596263576925-d90d63691097?w=800&q=80',
                'proof_returned_image' => 'https://images.unsplash.com/photo-1544253133-722a94593466?w=800&q=80',
                'created_at' => now()->subDays(6),
            ]);

            $conv2 = Conversation::create([
                'buyer_id' => $buyer2->id,
                'seller_id' => $seller2->id,
                'product_id' => $product2->id,
            ]);

            foreach ($chatLog2 as $log) {
                Message::create([
                    'conversation_id' => $conv2->id,
                    'sender_id' => ($log['sender'] === 'renter' ? $buyer2->id : $seller2->id),
                    'message' => $log['message'],
                    'created_at' => now()->subDays(1),
                ]);
            }
            }
        }

        // Add some more random returns
        $otherScenarios = [
            ['status' => 'checking', 'notes' => 'Barang baru sampai, sedang dicek kelengkapannya.'],
            ['status' => 'completed', 'notes' => 'Transaksi selesai, semua barang kembali lengkap.'],
            ['status' => 'pending', 'notes' => 'Penyewa belum update resi pengembalian.'],
        ];

        foreach ($otherScenarios as $idx => $os) {
            $buyer = $buyers[($idx + 1) % $buyers->count()];
            $seller = $sellers[($idx + 1) % $sellers->count()];
            $product = Product::where('store_id', $seller->store->id)->where('is_rental', true)->first();

            if (!$product) continue;

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
                'expected_date' => now()->subDays(5),
                'late_fee' => 0,
                'damage_fee' => 0,
                'created_at' => now()->subDays(10),
            ]);
        }
    }
}
