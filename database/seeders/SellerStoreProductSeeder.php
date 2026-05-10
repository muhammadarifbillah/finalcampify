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
                'buy_price' => 500000,
                'rent_price' => 0,
                'stock' => 12,
            ],
            [
                'name' => 'Sleeping Bag Thermal',
                'category' => 'Sleeping Bag',
                'jenis_produk' => 'jual',
                'buy_price' => 200000,
                'rent_price' => 0,
                'stock' => 25,
            ],
            [
                'name' => 'Kompor Portable Camping',
                'category' => 'Alat Masak',
                'jenis_produk' => 'jual',
                'buy_price' => 120000,
                'rent_price' => 0,
                'stock' => 18,
            ],
            [
                'name' => 'Matras Camping',
                'category' => 'Aksesoris',
                'jenis_produk' => 'sewa',
                'buy_price' => 100000,
                'rent_price' => 10000,
                'stock' => 30,
            ],
            [
                'name' => 'Backpack Hiking 60L',
                'category' => 'Tas Gunung',
                'jenis_produk' => 'jual',
                'buy_price' => 400000,
                'rent_price' => 0,
                'stock' => 10,
            ],
            [
                'name' => 'Jaket Outdoor Waterproof',
                'category' => 'Pakaian',
                'jenis_produk' => 'jual',
                'buy_price' => 250000,
                'rent_price' => 0,
                'stock' => 14,
            ],
            [
                'name' => 'Sepatu Hiking Trail',
                'category' => 'Sepatu',
                'jenis_produk' => 'jual',
                'buy_price' => 350000,
                'rent_price' => 0,
                'stock' => 8,
            ],
            [
                'name' => 'Headlamp LED Rechargeable',
                'category' => 'Aksesoris',
                'jenis_produk' => 'jual',
                'buy_price' => 75000,
                'rent_price' => 0,
                'stock' => 40,
            ],
            [
                'name' => 'Kursi Lipat Camping',
                'category' => 'Aksesoris',
                'jenis_produk' => 'sewa',
                'buy_price' => 150000,
                'rent_price' => 10000,
                'stock' => 16,
            ],
            [
                'name' => 'Trekking Pole (Sepasang)',
                'category' => 'Aksesoris',
                'jenis_produk' => 'sewa',
                'buy_price' => 150000,
                'rent_price' => 10000,
                'stock' => 22,
            ],
        ];

        $seed = crc32((string) $store->id);
        $poolCollection = collect($pool)->sortBy(fn ($item) => crc32($item['name'] . '|' . $seed))->values();
        $rentalSelected = $poolCollection->where('jenis_produk', 'sewa')->take(2)->values();
        $buySelected = $poolCollection->where('jenis_produk', '!=', 'sewa')->take(4)->values();
        $selected = $rentalSelected->concat($buySelected)->take(6)->values();

        $statusPattern = ['waiting', 'waiting', 'approved', 'approved', 'rejected', 'approved'];

        foreach ($selected as $index => $base) {
            $isRental = $base['jenis_produk'] === 'sewa';
            $status = $isRental ? 'approved' : ($statusPattern[$index] ?? 'approved');
            $description = 'Produk outdoor berkualitas untuk aktivitas camping & hiking. ' . Str::ucfirst(Str::random(8));
            $price = $base['buy_price'] > 0 ? $base['buy_price'] : $base['rent_price'];

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
                    'status' => $status,
                    'jenis_produk' => $base['jenis_produk'],
                    'is_rental' => $isRental,
                    'image' => 'https://via.placeholder.com/640x400?text=' . urlencode($base['name']),
                    'gambar' => 'https://via.placeholder.com/640x400?text=' . urlencode($base['name']),
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
