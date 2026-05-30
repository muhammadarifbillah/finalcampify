<?php

namespace Database\Seeders;

use App\Models\Courier;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SellerStoreProductSeeder extends Seeder
{
    public function run(): void
    {
        $sellerSpecs = [
            [
                'email' => 'slamet.outdoor@gmail.com',
                'name' => 'Slamet Riyadi',
                'store' => [
                    'nama_toko' => 'Gunung Slamet Outdoor',
                    'status' => 'active',
                    'alamat' => 'Jl. Kalisari No. 12, Purwokerto',
                    'deskripsi' => 'Spesialis perlengkapan mendaki gunung dan alat camping.',
                    'bank_name' => 'BCA',
                    'bank_account_number' => '1112223334',
                    'bank_account_name' => 'Slamet Riyadi',
                ],
            ],
            [
                'email' => 'ahmad.rimba@gmail.com',
                'name' => 'Ahmad Fauzi',
                'store' => [
                    'nama_toko' => 'Rimba Adventure',
                    'status' => 'active',
                    'alamat' => 'Jl. Merdeka No. 10, Bandung',
                    'deskripsi' => 'Perlengkapan camping & hiking kualitas premium, ready stock.',
                    'bank_name' => 'Mandiri',
                    'bank_account_number' => '1234567890',
                    'bank_account_name' => 'Ahmad Fauzi',
                ],
            ],
            [
                'email' => 'linda.gear@gmail.com',
                'name' => 'Linda Sari',
                'store' => [
                    'nama_toko' => 'Lembah Hijau Gear',
                    'status' => 'active',
                    'alamat' => 'Jl. Sudirman No. 8, Jakarta',
                    'deskripsi' => 'Sewa & jual alat outdoor dengan kualitas terbaik.',
                    'bank_name' => 'BCA',
                    'bank_account_number' => '9876543210',
                    'bank_account_name' => 'Linda Sari',
                ],
            ],
            [
                'email' => 'dani.puncak@gmail.com',
                'name' => 'Dani Ramadhan',
                'store' => [
                    'nama_toko' => 'Puncak Jaya Rental',
                    'status' => 'active',
                    'alamat' => 'Jl. Diponegoro No. 21, Surabaya',
                    'deskripsi' => 'Fokus penyewaan tenda, matras, dan alat masak camping.',
                    'bank_name' => 'BRI',
                    'bank_account_number' => '1122334455',
                    'bank_account_name' => 'Dani Ramadhan',
                ],
            ],
            [
                'email' => 'maya.alam@gmail.com',
                'name' => 'Maya Putri',
                'store' => [
                    'nama_toko' => 'Toko Sahabat Alam',
                    'status' => 'active',
                    'alamat' => 'Jl. Gatot Subroto No. 15, Jakarta',
                    'deskripsi' => 'Menyediakan segala kebutuhan petualangan alam terbuka Anda.',
                    'bank_name' => 'BCA',
                    'bank_account_number' => '4455667788',
                    'bank_account_name' => 'Maya Putri',
                ],
            ],
            [
                'email' => 'rizky.camp@gmail.com',
                'name' => 'Rizky Pratama',
                'store' => [
                    'nama_toko' => 'Rizky Camp Store',
                    'status' => 'active',
                    'alamat' => 'Jl. Dipatiukur No. 7, Bandung',
                    'deskripsi' => 'Sewa & jual gear hiking lengkap: carrier, matras, sleeping bag.',
                    'bank_name' => 'Mandiri',
                    'bank_account_number' => '2233445566',
                    'bank_account_name' => 'Rizky Pratama',
                ],
            ],
        ];

        $activeCouriers = Courier::where('status', 'aktif')->pluck('id')->all();

        foreach ($sellerSpecs as $spec) {
            $plainPassword = $this->passwordForEmail((string) ($spec['email'] ?? 'seller@campify.com'));
            $seller = User::updateOrCreate(
                ['email' => $spec['email']],
                [
                    'name' => $spec['name'],
                    'nama' => $spec['name'],
                    // Pola password seragam: (Email local part, capitalize) + "123?"
                    'password' => Hash::make($plainPassword),
                    'role' => 'seller',
                    'status' => 'active',
                ]
            );

            $store = Store::updateOrCreate(
                ['user_id' => $seller->id],
                array_merge(
                    [
                        'last_active' => now()->subDays(rand(0, 10)),
                        'logo' => null,
                        'catatan_admin' => null,
                        'latitude' => -6.20000000 + (rand(-50, 50) / 1000),
                        'longitude' => 106.81666667 + (rand(-50, 50) / 1000),
                    ],
                    $spec['store']
                )
            );

            $this->seedProductsForStore($store, $seller->id, $activeCouriers);
        }
    }

    private function passwordForEmail(string $email): string
    {
        $local = trim(explode('@', $email)[0] ?? '');
        $local = $local !== '' ? $local : 'seller';
        return Str::ucfirst($local) . '123?';
    }

    private function seedProductsForStore(Store $store, int $sellerUserId, array $courierIds): void
    {
        $pool = [
            [
                'name' => 'Tenda Dome 2 Orang',
                'category' => 'Tenda',
                'jenis_produk' => 'jual',
                'buy_price' => 750000,
                'rent_price' => 85000,
                'stock' => 12,
                'image_file' => 'tenda1.jpg',
            ],
            [
                'name' => 'Sleeping Bag Thermal Mummy',
                'category' => 'Sleeping Bag',
                'jenis_produk' => 'jual',
                'buy_price' => 350000,
                'rent_price' => 40000,
                'stock' => 25,
                'image_file' => 'sleepingbag1.jpeg',
            ],
            [
                'name' => 'Kompor Portable Camping',
                'category' => 'Alat Masak',
                'jenis_produk' => 'jual',
                'buy_price' => 180000,
                'rent_price' => 25000,
                'stock' => 18,
                'image_file' => 'alatmasak1.jpeg',
            ],
            [
                'name' => 'Sleeping Bag Premium Cabin',
                'category' => 'Sleeping Bag',
                'jenis_produk' => 'sewa',
                'buy_price' => 500000,
                'rent_price' => 55000,
                'stock' => 30,
                'image_file' => 'sleepingbag2.jpeg',
            ],
            [
                'name' => 'Backpack Hiking 60L',
                'category' => 'Tas Gunung',
                'jenis_produk' => 'jual',
                'buy_price' => 850000,
                'rent_price' => 95000,
                'stock' => 10,
                'image_file' => 'tasgunung1.jpeg',
            ],
            [
                'name' => 'Tenda Camping 4 Orang',
                'category' => 'Tenda',
                'jenis_produk' => 'jual',
                'buy_price' => 1200000,
                'rent_price' => 130000,
                'stock' => 14,
                'image_file' => 'tenda2.jpg',
            ],
            [
                'name' => 'Sepatu Hiking Trail',
                'category' => 'Sepatu',
                'jenis_produk' => 'jual',
                'buy_price' => 950000,
                'rent_price' => 120000,
                'stock' => 8,
                'image_file' => 'sepatu1.jpeg',
            ],
            [
                'name' => 'Lampu LED Camping Lantern',
                'category' => 'Alat Masak',
                'jenis_produk' => 'jual',
                'buy_price' => 150000,
                'rent_price' => 15000,
                'stock' => 40,
                'image_file' => 'alatmasak3.jpeg',
            ],
            [
                'name' => 'Sepatu Trekking Waterproof',
                'category' => 'Sepatu',
                'jenis_produk' => 'sewa',
                'buy_price' => 1100000,
                'rent_price' => 140000,
                'stock' => 16,
                'image_file' => 'sepatu2.jpeg',
            ],
            [
                'name' => 'Carrier Adventure 45L',
                'category' => 'Tas Gunung',
                'jenis_produk' => 'sewa',
                'buy_price' => 600000,
                'rent_price' => 65000,
                'stock' => 22,
                'image_file' => 'tasgunung3.jpeg',
            ],
        ];

        $seed = crc32((string) $store->id);
        $poolCollection = collect($pool)->sortBy(fn ($item) => crc32($item['name'] . '|' . $seed))->values();
        $rentalSelected = $poolCollection->where('jenis_produk', 'sewa')->take(2)->values();
        $buySelected = $poolCollection->where('jenis_produk', '!=', 'sewa')->take(4)->values();
        $selected = $rentalSelected->concat($buySelected)->take(6)->values();

        $statusPattern = ['waiting', 'waiting', 'approved', 'approved', 'rejected', 'approved'];

        $descriptions = [
            'Tenda Dome 2 Orang' => 'Tenda Dome premium berkapasitas 2 orang dengan struktur double layer tangguh. Memiliki indeks waterproof flysheet PU 3000mm tahan hujan deras, frame fiberglass solid berdiameter 7.9mm yang kokoh menahan angin kencang gunung, ventilasi jaring anti-nyamuk B3 yang breathable, serta berat ultra-ringan hanya 2.4kg. Sangat ideal untuk kenyamanan istirahat maksimal selama camping santai maupun trekking gunung.',
            'Tenda Camping 4 Orang' => 'Tenda camping keluarga berkapasitas 4 orang dengan pintu masuk ganda berlapis kelambu antinyamuk. Frame duralumin alloy super kokoh serta flysheet poliester PU 3000mm yang andal melindungi seluruh anggota keluarga dari angin kencang dan hujan lebat di dataran tinggi.',
            'Sleeping Bag Thermal Mummy' => 'Sleeping Bag (kantung tidur) tipe mummy kelas profesional dengan teknologi serat hollow fiber thermal insulation. Mampu menahan suhu ekstrim dingin hingga 5 derajat Celcius sekaligus tetap sejuk saat digunakan di udara hangat tropis. Dilengkapi resleting YKK anti-nyangkut di kedua sisi, tudung kepala ergonomis dengan tali pengencang, serta dilapisi bahan ripstop nylon 210T tahan air.',
            'Sleeping Bag Premium Cabin' => 'Sleeping Bag tipe cabin yang luas dan empuk dengan lapisan dalam katun flanel ekstra lembut. Sangat nyaman, menghangatkan tubuh di malam dingin pegunungan, serta dapat dibuka sepenuhnya untuk dijadikan selimut tebal saat berkemah bersama keluarga.',
            'Kompor Portable Camping' => 'Kompor portable ultra-kompak dengan sistem pemantik piezo-electric otomatis yang andal di segala kondisi cuaca. Dibuat dari material stainless steel dan aluminium alloy kualitas pesawat terbang yang anti-karat dan sangat kuat menahan beban wadah masak hingga 10kg. Sangat hemat gas butana dengan pelindung angin built-in terintegrasi, cocok untuk memasak cepat di area berkemah.',
            'Lampu LED Camping Lantern' => 'Lampu LED camping multifungsi berkekuatan 300 lumens dengan fitur rechargeable USB-C dan proteksi air IPX4. Sangat praktis digantung di dalam tenda atau ditaruh di atas meja untuk memberikan pencahayaan hangat 360 derajat selama malam hari di perkemahan.',
            'Backpack Hiking 60L' => 'Backpack/Carrier gunung berkapasitas 60 liter dengan teknologi ergonomis Airback Suspension System. Rangka internal aluminium ringan mendistribusikan beban secara merata ke pinggul untuk mencegah kelelahan punggung selama perjalanan panjang. Memiliki banyak kompartemen akses cepat, kantong hidrasi air, sabuk pinggang dengan bantalan tebal, serta dilengkapi rain cover 100% waterproof.',
            'Carrier Adventure 45L' => 'Tas carrier berkapasitas medium 45 liter yang didesain tangguh untuk petualangan akhir pekan atau weekend trekking. Dilengkapi backsystem dengan ventilasi udara optimal untuk meminimalkan keringat berlebih di punggung.',
            'Sepatu Hiking Trail' => 'Sepatu trekking gunung profesional berkontur tangguh dengan sol karet Vibram anti-selip yang mencengkeram kuat medan basah, licin, maupun berbatu. Bagian atas dilapisi kulit nubuck premium anti-air dipadu mesh bersirkulasi udara baik. Memiliki pelindung jari kaki karet tebal dan bantalan insole EVA empuk untuk menyerap benturan medan terjal.',
            'Sepatu Trekking Waterproof' => 'Sepatu gunung tahan air dengan membran bernapas berkualitas tinggi untuk melindungi kaki tetap kering di medan berlumpur maupun saat menyeberang sungai kecil. Dirancang dengan bantalan tumit anatomis untuk stabilitas maksimum di jalan curam.',
        ];

        foreach ($selected as $index => $base) {
            $isRental = $base['jenis_produk'] === 'sewa';
            $status = $isRental ? 'approved' : ($statusPattern[$index] ?? 'approved');
            $description = $descriptions[$base['name']] ?? 'Produk outdoor premium berkualitas tinggi untuk mendukung aktivitas petualangan camping, hiking, dan trekking Anda dengan aman dan nyaman.';
            $price = $base['buy_price'] > 0 ? $base['buy_price'] : $base['rent_price'];

            // Hitung dana jaminan: 25% harga barang jika ada, atau 3x harga sewa harian
            $escrowAmount = 0;
            if ($isRental) {
                $escrowAmount = $base['buy_price'] > 0
                    ? (int) round($base['buy_price'] * 0.25)
                    : (int) round($base['rent_price'] * 3);
            }

            $product = Product::firstOrCreate(
                [
                    'store_id' => $store->id,
                    'user_id' => $sellerUserId,
                    'name' => $base['name'],
                ],
                [
                    'nama_produk' => $base['name'],
                    'category' => $base['category'],
                    'kategori' => $base['category'],
                    'description' => $description,
                    'deskripsi' => $description,
                    'price' => $price,
                    'harga' => $price,
                    'buy_price' => $base['buy_price'],
                    'rent_price' => $base['rent_price'],
                    'escrow_amount' => $escrowAmount,
                    'status' => $status,
                    'jenis_produk' => $base['jenis_produk'],
                    'is_rental' => $isRental,
                    'image' => $base['image_file'],
                    'gambar' => $base['image_file'],
                    'stock' => $base['stock'],
                    'stok' => $base['stock'],
                    'rating' => rand(35, 50) / 10,
                    'reviews_count' => rand(0, 120),
                    'flag_reason' => $status === 'rejected' ? 'Konten perlu diperbaiki' : null,
                ]
            );

            if (!empty($courierIds)) {
                $product->couriers()->syncWithoutDetaching($courierIds);
            }
        }
    }
}
