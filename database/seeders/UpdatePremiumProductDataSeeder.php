<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UpdatePremiumProductDataSeeder extends Seeder
{
    /**
     * Peta peningkatan harga sewa dan deskripsi premium yang diselaraskan.
     */
    private array $premiumMap = [
        'tenda dome' => [
            'rent_price' => 85000,
            'desc' => 'Tenda Dome premium berkapasitas 2 orang dengan struktur double layer tangguh. Memiliki indeks waterproof flysheet PU 3000mm tahan hujan deras, frame fiberglass solid berdiameter 7.9mm yang kokoh menahan angin kencang gunung, ventilasi jaring anti-nyamuk B3 yang breathable, serta berat ultra-ringan hanya 2.4kg. Sangat ideal untuk kenyamanan istirahat maksimal selama camping santai maupun trekking gunung Mountain.'
        ],
        'tenda camping' => [
            'rent_price' => 130000,
            'desc' => 'Tenda camping keluarga berkapasitas 4 orang dengan pintu masuk ganda berlapis kelambu antinyamuk. Frame duralumin alloy super kokoh serta flysheet poliester PU 3000mm yang andal melindungi seluruh anggota keluarga dari angin kencang dan hujan lebat di dataran tinggi.'
        ],
        'tenda pramuka' => [
            'rent_price' => 180000,
            'desc' => 'Tenda pramuka/kelompok berkapasitas hingga 6 orang dengan struktur tiang penyangga baja galvanis tahan karat. Memiliki ruang tengah yang lapang untuk menaruh perlengkapan camping kelompok dengan aman.'
        ],
        'tenda' => [
            'rent_price' => 85000,
            'desc' => 'Tenda outdoor berspesifikasi tangguh dengan perlindungan anti air mutlak PU 3000mm dan frame kokoh penahan badai gunung.'
        ],
        'sleeping bag thermal' => [
            'rent_price' => 40000,
            'desc' => 'Sleeping Bag (kantung tidur) tipe mummy kelas profesional dengan teknologi serat hollow fiber thermal insulation. Mampu menahan suhu ekstrim dingin hingga 5 derajat Celcius sekaligus tetap sejuk saat digunakan di udara hangat tropis. Dilengkapi resleting YKK anti-nyangkut di kedua sisi, tudung kepala ergonomis dengan tali pengencang, serta dilapisi bahan ripstop nylon 210T tahan air.'
        ],
        'sleeping bag polar' => [
            'rent_price' => 25000,
            'desc' => 'Sleeping bag polar dengan lapisan dalam bulu sintetis berkualitas yang sangat halus di kulit. Menjaga suhu tubuh tetap stabil dan hangat saat berkemah di puncak gunung berkabut tebal.'
        ],
        'sleeping bag cabin' => [
            'rent_price' => 55000,
            'desc' => 'Sleeping Bag tipe cabin yang luas dan empuk dengan lapisan dalam katun flanel ekstra lembut. Sangat nyaman, menghangatkan tubuh di malam dingin pegunungan, serta dapat dibuka sepenuhnya untuk dijadikan selimut tebal saat berkemah bersama keluarga.'
        ],
        'sleeping bag' => [
            'rent_price' => 40000,
            'desc' => 'Sleeping bag isolasi termal empuk, sangat hangat menahan cuaca dingin gunung ekstrem demi tidur berkualitas Anda.'
        ],
        'kompor' => [
            'rent_price' => 25000,
            'desc' => 'Kompor portable ultra-kompak dengan sistem pemantik piezo-electric otomatis yang andal di segala kondisi cuaca. Dibuat dari material stainless steel dan aluminium alloy kualitas pesawat terbang yang anti-karat dan sangat kuat menahan beban wadah masak hingga 10kg. Sangat hemat gas butana dengan pelindung angin built-in terintegrasi, cocok untuk memasak cepat di area berkemah.'
        ],
        'lampu' => [
            'rent_price' => 20000,
            'desc' => 'Lampu LED camping multifungsi berkekuatan 300 lumens dengan fitur rechargeable USB-C dan proteksi air IPX4. Sangat praktis digantung di dalam tenda atau ditaruh di atas meja untuk memberikan pencahayaan hangat 360 derajat selama malam hari di perkemahan.'
        ],
        'cooking set' => [
            'rent_price' => 30000,
            'desc' => 'Satu set perlengkapan memasak luar ruangan terpadu yang terbuat dari bahan anodized aluminium ringan. Terdiri dari panci, wajan penggorengan, ketel air mini, dan mangkok saji yang ringkas disusun bertumpuk.'
        ],
        'backpack' => [
            'rent_price' => 95000,
            'desc' => 'Backpack/Carrier gunung berkapasitas 60 liter dengan teknologi ergonomis Airback Suspension System. Rangka internal aluminium ringan mendistribusikan beban secara merata ke pinggul untuk mencegah kelelahan punggung selama perjalanan panjang. Memiliki banyak kompartemen akses cepat, kantong hidrasi air, sabuk pinggang dengan bantalan tebal, serta dilengkapi rain cover 100% waterproof.'
        ],
        'carrier adventure' => [
            'rent_price' => 65000,
            'desc' => 'Tas carrier berkapasitas medium 45 liter yang didesain tangguh untuk petualangan akhir pekan atau weekend trekking. Dilengkapi backsystem dengan ventilasi udara optimal untuk meminimalkan keringat berlebih di punggung.'
        ],
        'carrier expedition' => [
            'rent_price' => 150000,
            'desc' => 'Carrier tas gunung ekspedisi berukuran jumbo 75L untuk pendakian panjang berhari-hari. Dibuat dari serat nilon Cordura anti-sobek serta sabuk pinggul berlapis busa tebal peredam berat beban.'
        ],
        'carrier' => [
            'rent_price' => 65000,
            'desc' => 'Tas carrier ransel hiking tangguh dengan suspensi berat ergonomis untuk pendakian gunung yang aman dan nyaman.'
        ],
        'sepatu hiking trail' => [
            'rent_price' => 120000,
            'desc' => 'Sepatu trekking gunung profesional berkontur tangguh dengan sol karet Vibram anti-selip yang mencengkeram kuat medan basah, licin, maupun berbatu. Bagian atas dilapisi kulit nubuck premium anti-air dipadu mesh bersirkulasi udara baik. Memiliki pelindung jari kaki karet tebal dan bantalan insole EVA empuk untuk menyerap benturan medan terjal.'
        ],
        'sepatu trekking' => [
            'rent_price' => 140000,
            'desc' => 'Sepatu gunung tahan air dengan membran bernapas berkualitas tinggi untuk melindungi kaki tetap kering di medan berlumpur maupun saat menyeberang sungai kecil. Dirancang dengan bantalan tumit anatomis untuk stabilitas maksimum di jalan curam.'
        ],
        'sepatu hiking gore-tex' => [
            'rent_price' => 170000,
            'desc' => 'Sepatu gunung bersertifikasi tinggi yang dilapisi membran Gore-Tex aktif untuk pertahanan air mutlak dan sirkulasi udara optimal bagi kaki Anda saat melintasi jalur ekstrem bersalju atau basah.'
        ],
        'sepatu' => [
            'rent_price' => 120000,
            'desc' => 'Sepatu mendaki gunung bersol Vibram anti-selip super tangguh untuk menghadapi segala rintangan jalur pegunungan.'
        ],
    ];

    public function run(): void
    {
        $products = Product::all();
        $updatedCount = 0;

        foreach ($products as $product) {
            $nameLower = strtolower($product->name ?? $product->nama_produk ?? '');
            $matched = false;

            foreach ($this->premiumMap as $keyword => $data) {
                if (str_contains($nameLower, $keyword)) {
                    $isRental = $product->jenis_produk === 'sewa' || $product->rent_price > 0 || $product->is_rental;

                    $product->description = $data['desc'];
                    $product->deskripsi = $data['desc'];
                    
                    if ($isRental) {
                        $product->rent_price = $data['rent_price'];
                        $product->price = $data['rent_price'];
                        $product->harga = $data['rent_price'];
                    }

                    $product->save();
                    $matched = true;
                    $updatedCount++;
                    break;
                }
            }

            // Fallback untuk yang tidak cocok keyword
            if (!$matched) {
                $defaultDesc = 'Peralatan aktivitas luar ruangan (outdoor) berspesifikasi tinggi untuk menunjang keamanan dan kenyamanan petualangan Anda di alam bebas.';
                $product->description = $defaultDesc;
                $product->deskripsi = $defaultDesc;

                $isRental = $product->jenis_produk === 'sewa' || $product->rent_price > 0 || $product->is_rental;
                if ($isRental && $product->rent_price > 0) {
                    // Naikkan harga sewa default sebesar 30% dan bulatkan
                    $newRent = ceil(($product->rent_price * 1.3) / 5000) * 5000;
                    $product->rent_price = $newRent;
                    $product->price = $newRent;
                    $product->harga = $newRent;
                }
                $product->save();
                $updatedCount++;
            }
        }

        $this->command->info("✅ Berhasil memperbarui {$updatedCount} data produk di database dengan harga sewa premium dan deskripsi terperinci!");
    }
}
