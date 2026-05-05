<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Chat;
use App\Models\Courier;
use App\Models\Product;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run admin user seeder
        $this->call(AdminUserSeeder::class);

        // Create additional regular users
        $users = User::factory()->count(10)->create();

        // Create stores with proper seller management fields
        $stores = Store::factory()->count(8)->create();
        $couriers = Courier::factory()->count(10)->create();

        // Create outdoor products (50 produk)
        $products = Product::factory()->count(50)->make()->each(function ($product) use ($stores, $couriers) {
            $product->store_id = $stores->random()->id;
            $product->save();

            $courierIds = $couriers->random(rand(1, 4))->pluck('id')->toArray();
            $product->couriers()->sync($courierIds);
        });

        // Create sample articles
        $articles = [
            [
                'title' => 'Cara Memilih Tenda Outdoor yang Tepat untuk Petualangan',
                'content' => 'Pelajari cara menyesuaikan ukuran, bahan, dan fitur tenda agar Anda nyaman saat berkemah di alam terbuka.',
                'kategori_slug' => 'outdoor',
                'status' => 'publish',
                'thumbnail' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80',
                'image' => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1200&q=80',
                'waktu_posting' => now()->subDays(12),
                'views' => rand(80, 160),
            ],
            [
                'title' => 'Tips Packing Ringkas untuk Pendaki dan Traveller',
                'content' => 'Jangan berat di ransel! Ikuti tips packing ini untuk membawa peralatan penting tanpa kehilangan kenyamanan.',
                'kategori_slug' => 'panduan',
                'status' => 'publish',
                'thumbnail' => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1200&q=80',
                'image' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80',
                'waktu_posting' => now()->subDays(9),
                'views' => rand(40, 120),
            ],
            [
                'title' => 'Review Sepatu Gunung: Kestabilan dan Kenyamanan di Medan Berat',
                'content' => 'Bandingkan beberapa sepatu gunung terbaik untuk memastikan Anda mendapatkan penguncian kaki yang aman dan bantalan empuk.',
                'kategori_slug' => 'review',
                'status' => 'draft',
                'thumbnail' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=1200&q=80',
                'image' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=1200&q=80',
                'waktu_posting' => now()->subDays(6),
                'views' => rand(10, 40),
            ],
            [
                'title' => 'Checklist Perlengkapan Camping untuk Akhir Pekan',
                'content' => 'Buat daftar perlengkapan camping lengkap dari tenda hingga peralatan memasak agar perjalanan akhir pekan Anda berjalan lancar.',
                'kategori_slug' => 'tips',
                'status' => 'publish',
                'thumbnail' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80',
                'image' => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1200&q=80',
                'waktu_posting' => now()->subDays(4),
                'views' => rand(120, 250),
            ],
            [
                'title' => 'Panduan Memilih Ransel yang Ringan dan Tahan Lama',
                'content' => 'Ransel yang tepat dapat mengubah pengalaman perjalanan Anda. Pelajari perbedaan bahan, kapasitas, dan fitur ergonomis.',
                'kategori_slug' => 'outdoor',
                'status' => 'draft',
                'thumbnail' => 'https://images.unsplash.com/photo-1473625247510-8ceb1760943f?auto=format&fit=crop&w=1200&q=80',
                'image' => 'https://images.unsplash.com/photo-1473625247510-8ceb1760943f?auto=format&fit=crop&w=1200&q=80',
                'waktu_posting' => now()->subDays(2),
                'views' => rand(15, 35),
            ],
        ];

        foreach ($articles as $articleData) {
            Article::create($articleData);
        }

        // Create transactions with proper timestamps for chart data
        for ($month = 1; $month <= 12; $month++) {
            $count = rand(2, 8);
            for ($i = 0; $i < $count; $i++) {
                Transaction::create([
                    'user_id' => $users->random()->id,
                    'product_id' => $products->random()->id,
                    'total' => fake()->numberBetween(50000, 1000000),
                    'created_at' => fake()->dateTimeBetween(
                        now()->year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01',
                        now()->year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-28'
                    ),
                    'updated_at' => now(),
                ]);
            }
        }

        // Create normal chats dengan pesan Bahasa Indonesia
        $normalMessages = [
            'Halo, apakah produk ini masih tersedia?',
            'Berapa lama waktu pengiriman ke Jakarta?',
            'Apakah ada diskon untuk pembelian dalam jumlah besar?',
            'Saya tertarik dengan produk ini, bisa dijelaskan spesifikasinya?',
            'Apakah bisa dicicil?',
            'Produk berkualitas tidak? Ada garansi?',
            'Kapan produk ini di-restock?',
            'Saya mau pesan 5 unit, berapa harganya?',
            'Apakah gratis ongkir?',
            'Bagaimana dengan garansi resmi?',
            'Apakah bisa custom warna?',
            'Saya sudah melakukan transfer, kapan barangnya dikirim?',
            'Kualitas barang bagus, terima kasih!',
            'Pengiriman cepat, saya puas dengan layanannya.',
            'Produk sesuai dengan deskripsi, recommended!',
        ];

        foreach ($normalMessages as $msg) {
            Chat::create([
                'user_id' => $users->random()->id,
                'message' => $msg,
                'is_flagged' => false,
            ]);
        }

        // Create problematic chats dengan pesan Bahasa Indonesia
        $problematicMessages = [
            'Saya sudah menunggu 2 minggu, barang belum datang! Ini penipuan!',
            'Barang yang saya terima rusak dan tidak sesuai dengan foto!',
            'Customer service kalian sangat buruk, tidak ada yang merespons!',
            'Saya mau refund! Barang tidak sesuai dengan deskripsi!',
            'Tolong cancel order saya, uang saya belum kembali!',
            'Barang original atau palsu? Kualitasnya jelek banget!',
            'Ini spam, stop kirim promosi!',
            'Berapa lama sih menunggu? Ini worst service ever!',
            'Barang rusak saat sampai, butuh ganti urgently!',
            'Pembayaran eror berkali-kali, tapi barangnya dipotong 2x!',
            'Harga berbeda dari yang dijanjikan, disitu katanya murah!',
            'Seller tidak responsif, sudah 3 hari tidak balas chat!',
            'Barangnya expired/kadaluarsa! Tidak bisa dipakai!',
            'KOMPLAIN!!! Barang tidak seperti di gambar!!!',
            'Saya ingin berbicara dengan manager, pelayanan sangat mengecewakan!',
            'Produk palsu! Sudah saya bandingkan dengan toko resmi!',
            'Pengiriman memakan waktu sangat lama, sangat tidak puas!',
            'Apakah ini sistem penipuan marketplace?',
        ];

        $users->take(5)->each(function ($user) use ($problematicMessages) {
            $selected = array_rand($problematicMessages, rand(2, 4));
            if (!is_array($selected)) {
                $selected = [$selected];
            }
            foreach ($selected as $index) {
                Chat::create([
                    'user_id' => $user->id,
                    'message' => $problematicMessages[$index],
                    'is_flagged' => true,
                ]);
            }
        });
    }
}
