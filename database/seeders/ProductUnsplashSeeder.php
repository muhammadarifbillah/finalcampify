<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductUnsplashSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Get or create a seller user
        $seller = User::where('email', 'seller@campify.com')->first();
        if (!$seller) {
            $seller = User::create([
                'name' => 'Campify Store Owner',
                'nama' => 'Campify Store Owner',
                'email' => 'seller@campify.com',
                'password' => bcrypt('password123'),
                'role' => 'seller',
                'phone' => '081234567890',
                'address' => 'Jl. Petualangan No. 123, Bandung',
                'status' => 'active',
            ]);
        }

        // Ensure the seller has a store associated
        $store = \App\Models\Store::updateOrCreate(
            ['user_id' => $seller->id],
            [
                'nama_toko' => 'Campify Adventure Hub',
                'status' => 'active',
                'alamat' => 'Jl. Petualangan No. 123, Bandung',
                'deskripsi' => 'Toko resmi Campify Hub penyedia perlengkapan camping dan trekking terlengkap.',
                'bank_name' => 'BCA',
                'bank_account_number' => '8887776665',
                'bank_account_name' => 'Campify Store Owner',
                'last_active' => now(),
                'latitude' => -6.20000000,
                'longitude' => 106.81666667,
            ]
        );

        // Map of product data with local filenames (from public/assets/images)
        $products = [
            [
                'name' => 'Tenda Dome 2 Orang',
                'category' => 'Tenda',
                'description' => 'Tenda Dome premium berkapasitas 2 orang dengan struktur double layer tangguh. Memiliki indeks waterproof flysheet PU 3000mm tahan hujan deras, frame fiberglass solid berdiameter 7.9mm yang kokoh menahan angin kencang gunung, ventilasi jaring anti-nyamuk B3 yang breathable, serta berat ultra-ringan hanya 2.4kg. Sangat ideal untuk kenyamanan istirahat maksimal selama camping santai maupun trekking gunung.',
                'price' => 750000,
                'rent_price' => 85000,
                'is_rental' => true,
                'stock' => 10,
                'image_file' => 'tenda1.jpg'
            ],
            [
                'name' => 'Sleeping Bag Thermal Mummy',
                'category' => 'Sleeping Bag',
                'description' => 'Sleeping Bag (kantung tidur) tipe mummy kelas profesional dengan teknologi serat hollow fiber thermal insulation. Mampu menahan suhu ekstrim dingin hingga 5 derajat Celcius sekaligus tetap sejuk saat digunakan di udara hangat tropis. Dilengkapi resleting YKK anti-nyangkut di kedua sisi, tudung kepala ergonomis dengan tali pengencang, serta dilapisi bahan ripstop nylon 210T tahan air.',
                'price' => 350000,
                'rent_price' => 40000,
                'is_rental' => true,
                'stock' => 15,
                'image_file' => 'sleepingbag1.jpeg'
            ],
            [
                'name' => 'Backpack Hiking 60L',
                'category' => 'Tas Gunung',
                'description' => 'Backpack/Carrier gunung berkapasitas 60 liter dengan teknologi ergonomis Airback Suspension System. Rangka internal aluminium ringan mendistribusikan beban secara merata ke pinggul untuk mencegah kelelahan punggung selama perjalanan panjang. Memiliki banyak kompartemen akses cepat, kantong hidrasi air, sabuk pinggang dengan bantalan tebal, serta dilengkapi rain cover 100% waterproof.',
                'price' => 850000,
                'rent_price' => 95000,
                'is_rental' => true,
                'stock' => 8,
                'image_file' => 'tasgunung1.jpeg'
            ],
            [
                'name' => 'Sleeping Bag Polar Hooded',
                'category' => 'Sleeping Bag',
                'description' => 'Sleeping bag polar dengan lapisan dalam bulu sintetis berkualitas yang sangat halus di kulit. Menjaga suhu tubuh tetap stabil dan hangat saat berkemah di puncak gunung berkabut tebal.',
                'price' => 250000,
                'rent_price' => 25000,
                'is_rental' => true,
                'stock' => 12,
                'image_file' => 'sleepingbag2.jpeg'
            ],
            [
                'name' => 'Kompor Portable Camping',
                'category' => 'Alat Masak',
                'description' => 'Kompor portable ultra-kompak dengan sistem pemantik piezo-electric otomatis yang andal di segala kondisi cuaca. Dibuat dari material stainless steel dan aluminium alloy kualitas pesawat terbang yang anti-karat dan sangat kuat menahan beban wadah masak hingga 10kg. Sangat hemat gas butana dengan pelindung angin built-in terintegrasi, cocok untuk memasak cepat di area berkemah.',
                'price' => 180000,
                'rent_price' => 25000,
                'is_rental' => true,
                'stock' => 20,
                'image_file' => 'alatmasak1.jpeg'
            ],
            [
                'name' => 'Lampu LED Camping Lantern',
                'category' => 'Alat Masak',
                'description' => 'Lampu LED camping multifungsi berkekuatan 300 lumens dengan fitur rechargeable USB-C dan proteksi air IPX4. Sangat praktis digantung di dalam tenda atau ditaruh di atas meja untuk memberikan pencahayaan hangat 360 derajat selama malam hari di perkemahan.',
                'price' => 150000,
                'rent_price' => 20000,
                'is_rental' => true,
                'stock' => 25,
                'image_file' => 'alatmasak3.jpeg'
            ],
            [
                'name' => 'Tenda Camping 4 Orang',
                'category' => 'Tenda',
                'description' => 'Tenda camping keluarga berkapasitas 4 orang dengan pintu masuk ganda berlapis kelambu antinyamuk. Frame duralumin alloy super kokoh serta flysheet poliester PU 3000mm yang andal melindungi seluruh anggota keluarga dari angin kencang dan hujan lebat di dataran tinggi.',
                'price' => 1200000,
                'rent_price' => 130000,
                'is_rental' => true,
                'stock' => 6,
                'image_file' => 'tenda2.jpg'
            ],
            [
                'name' => 'Tenda Pramuka Double Layer',
                'category' => 'Tenda',
                'description' => 'Tenda pramuka/kelompok berkapasitas hingga 6 orang dengan struktur tiang penyangga baja galvanis tahan karat. Memiliki ruang tengah yang lapang untuk menaruh perlengkapan camping kelompok dengan aman.',
                'price' => 1800000,
                'rent_price' => 180000,
                'is_rental' => true,
                'stock' => 4,
                'image_file' => 'tenda3.jpg'
            ],
            [
                'name' => 'Sepatu Hiking Trail',
                'category' => 'Sepatu',
                'description' => 'Sepatu trekking gunung profesional berkontur tangguh dengan sol karet Vibram anti-selip yang mencengkeram kuat medan basah, licin, maupun berbatu. Bagian atas dilapisi kulit nubuck premium anti-air dipadu mesh bersirkulasi udara baik. Memiliki pelindung jari kaki karet tebal dan bantalan insole EVA empuk untuk menyerap benturan medan terjal.',
                'price' => 950000,
                'rent_price' => 120000,
                'is_rental' => true,
                'stock' => 9,
                'image_file' => 'sepatu1.jpeg'
            ],
            [
                'name' => 'Sepatu Trekking Waterproof',
                'category' => 'Sepatu',
                'description' => 'Sepatu gunung tahan air dengan membran bernapas berkualitas tinggi untuk melindungi kaki tetap kering di medan berlumpur maupun saat menyeberang sungai kecil. Dirancang dengan bantalan tumit anatomis untuk stabilitas maksimum di jalan curam.',
                'price' => 1100000,
                'rent_price' => 140000,
                'is_rental' => true,
                'stock' => 5,
                'image_file' => 'sepatu2.jpeg'
            ],
            [
                'name' => 'Sepatu Hiking Gore-Tex',
                'category' => 'Sepatu',
                'description' => 'Sepatu gunung bersertifikasi tinggi yang dilapisi membran Gore-Tex aktif untuk pertahanan air mutlak dan sirkulasi udara optimal bagi kaki Anda saat melintasi jalur ekstrem bersalju atau basah.',
                'price' => 1600000,
                'rent_price' => 170000,
                'is_rental' => true,
                'stock' => 7,
                'image_file' => 'sepatu3.jpeg'
            ],
            [
                'name' => 'Sleeping Bag Premium Cabin',
                'category' => 'Sleeping Bag',
                'description' => 'Sleeping Bag tipe cabin yang luas dan empuk dengan lapisan dalam katun flanel ekstra lembut. Sangat nyaman, menghangatkan tubuh di malam dingin pegunungan, serta dapat dibuka sepenuhnya untuk dijadikan selimut tebal saat berkemah bersama keluarga.',
                'price' => 500000,
                'rent_price' => 55000,
                'is_rental' => true,
                'stock' => 15,
                'image_file' => 'sleepingbag3.jpeg'
            ],
            [
                'name' => 'Cooking Set Nesting DS-308',
                'category' => 'Alat Masak',
                'description' => 'Satu set perlengkapan memasak luar ruangan terpadu yang terbuat dari bahan anodized aluminium ringan. Terdiri dari panci, wajan penggorengan, ketel air mini, dan mangkok saji yang ringkas disusun bertumpuk.',
                'price' => 280000,
                'rent_price' => 30000,
                'is_rental' => true,
                'stock' => 10,
                'image_file' => 'alatmasak2.jpeg'
            ],
            [
                'name' => 'Carrier Adventure 45L',
                'category' => 'Tas Gunung',
                'description' => 'Tas carrier berkapasitas medium 45 liter yang didesain tangguh untuk petualangan akhir pekan atau weekend trekking. Dilengkapi backsystem dengan ventilasi udara optimal untuk meminimalkan keringat berlebih di punggung.',
                'price' => 600000,
                'rent_price' => 65000,
                'is_rental' => true,
                'stock' => 12,
                'image_file' => 'tasgunung2.jpeg'
            ],
            [
                'name' => 'Carrier Expedition 75L',
                'category' => 'Tas Gunung',
                'description' => 'Carrier tas gunung ekspedisi berukuran jumbo 75L untuk pendakian panjang berhari-hari. Dibuat dari serat nilon Cordura anti-sobek serta sabuk pinggul berlapis busa tebal peredam berat beban.',
                'price' => 1350000,
                'rent_price' => 150000,
                'is_rental' => true,
                'stock' => 8,
                'image_file' => 'tasgunung3.jpeg'
            ],
        ];

        foreach ($products as $p) {
            $filename = $p['image_file'];

            $productData = [
                'name' => $p['name'],
                'category' => $p['category'],
                'description' => $p['description'],
                'price' => $p['price'],
                'buy_price' => $p['price'],
                'rent_price' => $p['rent_price'],
                'is_rental' => $p['is_rental'],
                'jenis_produk' => 'sewa',
                'stock' => $p['stock'],
                // store filename only; API will return full URL to /assets/images/
                'image' => $filename,
                'status' => 'approved',
                'user_id' => $seller->id,
                'store_id' => $store->id,
            ];

            Product::create($productData);
        }
    }
}