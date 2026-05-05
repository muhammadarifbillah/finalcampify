<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Chat;
use App\Models\ChatbotResponse;
use App\Models\Conversation;
use App\Models\Courier;
use App\Models\Message;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Report;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Violation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(AdminUserSeeder::class);

        $admin = User::where('email', 'admin@campify.com')->first();
        $sampleBuyer = User::where('email', 'buyer@campify.com')->first();
        $sampleSeller = User::where('email', 'seller@campify.com')->first();

        $buyers = collect([$sampleBuyer])->merge(
            collect(range(1, 8))->map(fn ($i) => User::updateOrCreate(
                ['email' => "pembeli{$i}@campify.test"],
                [
                    'name' => "Pembeli {$i}",
                    'nama' => "Pembeli {$i}",
                    'password' => Hash::make('password'),
                    'role' => 'buyer',
                    'status' => 'active',
                    'address' => "Jl. Campify Buyer {$i}",
                    'city' => fake()->randomElement(['Jakarta', 'Bandung', 'Bogor', 'Depok']),
                    'district' => fake()->randomElement(['Gambir', 'Menteng', 'Coblong', 'Lengkong']),
                    'postal_code' => (string) fake()->numberBetween(10000, 49999),
                    'phone' => '0812' . fake()->numerify('########'),
                    'last_login' => now()->subDays(fake()->numberBetween(0, 14)),
                ]
            ))
        )->filter()->values();

        $sellers = collect([$sampleSeller])->merge(
            collect(range(1, 5))->map(fn ($i) => User::updateOrCreate(
                ['email' => "penjual{$i}@campify.test"],
                [
                    'name' => "Penjual {$i}",
                    'nama' => "Penjual {$i}",
                    'password' => Hash::make('password'),
                    'role' => 'seller',
                    'status' => 'active',
                    'address' => "Jl. Campify Seller {$i}",
                    'city' => fake()->randomElement(['Jakarta', 'Bandung', 'Bogor', 'Depok']),
                    'district' => fake()->randomElement(['Gambir', 'Menteng', 'Coblong', 'Lengkong']),
                    'postal_code' => (string) fake()->numberBetween(10000, 49999),
                    'phone' => '0821' . fake()->numerify('########'),
                    'last_login' => now()->subDays(fake()->numberBetween(0, 10)),
                ]
            ))
        )->filter()->values();

        $stores = $sellers->map(function ($seller, $index) {
            return Store::updateOrCreate(
                ['user_id' => $seller->id],
                [
                    'nama_toko' => 'Campify Outdoor ' . ($index + 1),
                    'status' => 'active',
                    'last_active' => now()->subDays(fake()->numberBetween(0, 7)),
                    'deskripsi' => 'Toko outdoor terpercaya untuk alat camping, hiking, dan rental perlengkapan.',
                    'alamat' => $seller->address ?? 'Jl. Campify Seller',
                    'logo' => 'https://via.placeholder.com/200x200?text=C',
                    'catatan_admin' => null,
                    'alasan_ban' => null,
                ]
            );
        });

        $couriers = collect([
            ['JNE Camp', 'REG', '2-3 hari', 15000],
            ['SiCepat Outdoor', 'BEST', '1-2 hari', 22000],
            ['GoSend Campify', 'Instant', '1 hari', 25000],
            ['J&T Adventure', 'EZ', '2-4 hari', 18000],
            ['Anteraja Trail', 'Regular', '2-3 hari', 16000],
        ])->map(fn ($row) => Courier::create([
            'name' => $row[0],
            'service' => $row[1],
            'estimate' => $row[2],
            'price' => $row[3],
            'status' => 'aktif',
        ]));

        $productCatalog = [
            ['Tenda Camping 2 Orang', 'Tenda', 450000, 65000],
            ['Tenda Family 4 Orang', 'Tenda', 950000, 120000],
            ['Sleeping Bag Thermal', 'Sleeping Bag', 275000, 35000],
            ['Carrier Hiking 60L', 'Backpack', 650000, 70000],
            ['Kompor Portable Camping', 'Cookware', 220000, 25000],
            ['Headlamp Rechargeable', 'Lighting', 125000, 15000],
            ['Matras Camping Foam', 'Matras', 90000, 12000],
            ['Sepatu Hiking Waterproof', 'Sepatu', 780000, 0],
            ['Jaket Outdoor Waterproof', 'Pakaian', 520000, 0],
            ['Trekking Pole Carbon', 'Safety', 180000, 20000],
            ['Cooking Set Nesting', 'Cookware', 300000, 30000],
            ['Water Filter Portable', 'Accessories', 240000, 0],
        ];

        $products = collect($productCatalog)->map(function ($item, $index) use ($stores, $couriers) {
            $store = $stores[$index % $stores->count()];
            $product = Product::create([
                'store_id' => $store->id,
                'user_id' => $store->user_id,
                'name' => $item[0],
                'nama_produk' => $item[0],
                'category' => $item[1],
                'kategori' => $item[1],
                'description' => 'Produk outdoor berkualitas, siap dipakai untuk camping dan perjalanan alam.',
                'deskripsi' => 'Produk outdoor berkualitas, siap dipakai untuk camping dan perjalanan alam.',
                'price' => $item[2],
                'harga' => $item[2],
                'buy_price' => $item[2],
                'rent_price' => $item[3],
                'status' => 'approved',
                'is_rental' => $item[3] > 0,
                'jenis_produk' => $item[3] > 0 ? 'sewa' : 'jual',
                'rating' => 0,
                'reviews_count' => 0,
                'image' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=80',
                'gambar' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=80',
                'stock' => fake()->numberBetween(5, 40),
                'stok' => fake()->numberBetween(5, 40),
            ]);

            $product->couriers()->sync($couriers->random(min(3, $couriers->count()))->pluck('id')->all());

            return $product;
        });

        $articles = [
            ['Cara Memilih Tenda Outdoor yang Tepat', 'outdoor', 'publish'],
            ['Tips Packing Ringkas untuk Pendaki', 'panduan', 'publish'],
            ['Checklist Perlengkapan Camping Akhir Pekan', 'tips', 'publish'],
            ['Merawat Peralatan Rental Agar Awet', 'review', 'draft'],
        ];

        foreach ($articles as [$title, $category, $status]) {
            Article::create([
                'title' => $title,
                'content' => fake()->paragraph(5),
                'kategori_slug' => $category,
                'status' => $status,
                'thumbnail' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80',
                'image' => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1200&q=80',
                'waktu_posting' => now()->subDays(fake()->numberBetween(1, 20)),
                'views' => fake()->numberBetween(10, 300),
            ]);
        }

        foreach ([
            'pengiriman' => 'Pengiriman tersedia melalui JNE, SiCepat, GoSend, J&T, dan Anteraja.',
            'rental' => 'Produk rental wajib dikembalikan sesuai durasi sewa yang dipilih.',
            'refund' => 'Refund diproses oleh admin setelah bukti valid diterima.',
            'kontak' => 'Hubungi admin Campify melalui halaman chat bantuan.',
        ] as $keyword => $response) {
            ChatbotResponse::create(compact('keyword', 'response'));
        }

        foreach (range(1, 12) as $i) {
            Chat::create([
                'user_id' => $buyers->random()->id,
                'message' => fake()->randomElement([
                    'Apakah produk ini masih tersedia?',
                    'Berapa ongkir ke Bandung?',
                    'Bisa sewa untuk akhir pekan?',
                    'Kapan barang dikirim?',
                    'Produk sesuai foto, terima kasih.',
                ]),
                'is_flagged' => $i % 5 === 0,
            ]);
        }

        foreach ($buyers->take(6) as $buyer) {
            $product = $products->random();
            $conversation = Conversation::firstOrCreate([
                'product_id' => $product->id,
                'buyer_id' => $buyer->id,
                'seller_id' => $product->sellerUserId(),
            ]);

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $buyer->id,
                'message' => 'Halo, produk ini masih tersedia?',
            ]);

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $conversation->seller_id,
                'message' => 'Halo, masih tersedia. Silakan checkout kapan saja.',
            ]);

            $conversation->touch();
        }

        foreach ($buyers->take(3) as $buyer) {
            $product = $products->random();
            $report = Report::create([
                'reporter_id' => $buyer->id,
                'seller_id' => $product->sellerUserId(),
                'product_id' => $product->id,
                'reason' => fake()->randomElement(['Harga tidak wajar', 'Deskripsi menyesatkan', 'Produk mencurigakan']),
                'description' => fake()->sentence(),
                'status' => 'pending',
            ]);

            if (fake()->boolean(35)) {
                Violation::create([
                    'seller_id' => $report->seller_id,
                    'admin_id' => $admin?->id,
                    'report_id' => $report->id,
                    'product_id' => $report->product_id,
                    'source' => 'report',
                    'action' => 'warning',
                    'strike_count' => 1,
                    'reason' => $report->reason,
                ]);
            }
        }

        $orders = collect(range(1, 14))->map(function ($i) use ($buyers, $products) {
            $buyer = $buyers->random();
            $items = $products->random(fake()->numberBetween(1, 3));
            $subtotal = $items->sum(fn ($product) => (int) ($product->buy_price ?: $product->price));
            $shipping = fake()->randomElement([15000, 18000, 22000, 25000]);

            $order = Order::create([
                'user_id' => $buyer->id,
                'receiver_name' => $buyer->name,
                'total' => $subtotal + $shipping,
                'shipping_address' => $buyer->address,
                'shipping_city' => $buyer->city,
                'shipping_district' => $buyer->district,
                'shipping_postal_code' => $buyer->postal_code,
                'shipping_phone' => $buyer->phone,
                'metode_pembayaran' => fake()->randomElement(['transfer', 'cod', 'ewallet']),
                'status' => fake()->randomElement(['menunggu', 'diproses', 'dikirim', 'selesai']),
                'kurir' => fake()->randomElement(['jne', 'gosend', 'sicepat']),
                'no_resi' => $i % 3 === 0 ? 'CPY' . fake()->numerify('########') : null,
            ]);

            foreach ($items as $product) {
                $type = $product->rent_price > 0 && fake()->boolean(45) ? 'rent' : 'buy';
                $duration = $type === 'rent' ? fake()->numberBetween(1, 5) : null;
                $harga = $type === 'rent'
                    ? (int) $product->rent_price * $duration
                    : (int) ($product->buy_price ?: $product->price);

                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'qty' => fake()->numberBetween(1, 2),
                    'harga' => $harga,
                    'type' => $type,
                    'duration' => $duration,
                    'start_date' => $type === 'rent' ? now()->addDays(fake()->numberBetween(1, 8))->toDateString() : null,
                ]);
            }

            return $order;
        });

        foreach ($buyers->take(5) as $buyer) {
            foreach ($products->random(2) as $product) {
                DB::table('keranjang')->updateOrInsert(
                    ['user_id' => $buyer->id, 'product_id' => $product->id],
                    [
                        'qty' => fake()->numberBetween(1, 2),
                        'type' => $product->rent_price > 0 ? fake()->randomElement(['buy', 'rent']) : 'buy',
                        'duration' => $product->rent_price > 0 ? fake()->numberBetween(1, 4) : null,
                        'start_date' => $product->rent_price > 0 ? now()->addDays(3)->toDateString() : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                DB::table('wishlists')->updateOrInsert(
                    ['user_id' => $buyer->id, 'product_id' => $product->id],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        $rentalDetails = OrderDetail::with('order')
            ->where('type', 'rent')
            ->limit(5)
            ->get();

        foreach ($rentalDetails as $detail) {
            $rentalId = DB::table('rentals')->insertGetId([
                'user_id' => $detail->order->user_id,
                'product_id' => $detail->product_id,
                'order_id' => $detail->order_id,
                'start_date' => $detail->start_date,
                'end_date' => optional($detail->start_date)->addDays($detail->duration ?? 1)?->toDateString(),
                'duration' => $detail->duration,
                'price' => $detail->harga,
                'status' => fake()->randomElement(['active', 'returned']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (fake()->boolean(40)) {
                DB::table('returns')->insert([
                    'rental_id' => $rentalId,
                    'resi_return' => 'RET' . fake()->numerify('########'),
                    'bukti_denda' => null,
                    'kondisi_barang' => fake()->randomElement(['baik', 'lecet ringan']),
                    'denda' => fake()->randomElement([0, 0, 10000, 20000]),
                    'tanggal_pengembalian' => now()->subDays(fake()->numberBetween(0, 3)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        foreach ($orders->where('status', 'selesai')->take(8) as $order) {
            foreach ($order->details as $detail) {
                $comment = fake()->randomElement([
                    'Barang bagus dan sesuai deskripsi.',
                    'Seller responsif, pengiriman cepat.',
                    'Kualitas cukup baik untuk camping.',
                    'Rental mudah dan alat bersih.',
                ]);

                DB::table('product_ratings')->insert([
                    'user_id' => $order->user_id,
                    'product_id' => $detail->product_id,
                    'order_id' => $order->id,
                    'rating' => fake()->numberBetween(4, 5),
                    'comment' => $comment,
                    'ulasan' => $comment,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        foreach ($stores as $store) {
            foreach ($buyers->random(min(3, $buyers->count())) as $buyer) {
                $comment = fake()->randomElement([
                    'Toko ramah dan cepat membalas.',
                    'Produk lengkap, cocok untuk persiapan camping.',
                    'Pelayanan memuaskan.',
                ]);

                DB::table('store_ratings')->insert([
                    'user_id' => $buyer->id,
                    'store_id' => $store->user_id,
                    'order_id' => null,
                    'rating' => fake()->numberBetween(4, 5),
                    'comment' => $comment,
                    'ulasan' => $comment,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        foreach ($products as $product) {
            $ratings = DB::table('product_ratings')->where('product_id', $product->id);
            $count = (clone $ratings)->count();
            $avg = $count > 0 ? round((clone $ratings)->avg('rating'), 1) : 0;
            $product->update(['rating' => $avg, 'reviews_count' => $count]);
        }

        foreach (range(1, 24) as $i) {
            Transaction::create([
                'user_id' => $buyers->random()->id,
                'product_id' => $products->random()->id,
                'total' => fake()->numberBetween(50000, 1500000),
                'created_at' => now()->subDays(fake()->numberBetween(0, 90)),
                'updated_at' => now(),
            ]);
        }

        $admin?->forceFill(['last_login' => now()])->save();
    }
}
