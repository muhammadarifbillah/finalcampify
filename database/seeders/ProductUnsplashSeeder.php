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
                'name' => 'Campify Store',
                'email' => 'seller@campify.com',
                'password' => bcrypt('password123'),
                'role' => 'seller',
                'phone' => '081234567890',
                'address' => 'Jl. Petualangan No. 123, Bandung',
            ]);
        }

        // Map of product data with local filenames (from public/assets/images)
        $products = [
            ['name' => 'Tenda Dome 2 Orang', 'category' => 'Tenda', 'description' => 'Tenda dome berkualitas tinggi cocok untuk 2 orang. Material waterproof dan tahan angin kuat.', 'price' => 450000, 'rent_price' => 50000, 'is_rental' => true, 'stock' => 10, 'image_file' => 'Tenda1.jpg'],
            ['name' => 'Sleeping Bag Premium', 'category' => 'Sleeping Bag', 'description' => 'Sleeping bag dengan teknologi thermal, cocok untuk musim dingin dan panas.', 'price' => 350000, 'rent_price' => 30000, 'is_rental' => true, 'stock' => 15, 'image_file' => 'sleepingbag1.jpeg'],
            ['name' => 'Backpack 60L', 'category' => 'Backpack', 'description' => 'Backpack hiking profesional dengan kapasitas 60 liter, ergonomis dan tahan lama.', 'price' => 550000, 'rent_price' => 40000, 'is_rental' => true, 'stock' => 8, 'image_file' => 'tasgunung1.jpeg'],
            ['name' => 'Matras Camping Inflatable', 'category' => 'Matras', 'description' => 'Matras camping yang dapat dikembung, ringan dan nyaman untuk tidur outdoor.', 'price' => 200000, 'rent_price' => 15000, 'is_rental' => true, 'stock' => 12, 'image_file' => 'alatmasak1.jpeg'],
            ['name' => 'Kompor Portable', 'category' => 'Peralatan Masak', 'description' => 'Kompor portable gas untuk memasak di alam bebas, ringan dan praktis.', 'price' => 150000, 'rent_price' => 12000, 'is_rental' => true, 'stock' => 20, 'image_file' => 'alatmasak2.jpeg'],
            ['name' => 'Lampu LED Camping', 'category' => 'Pencahayaan', 'description' => 'Lampu LED multi-fungsi untuk camping, hemat energi dan terang maksimal.', 'price' => 100000, 'rent_price' => 8000, 'is_rental' => true, 'stock' => 25, 'image_file' => 'alatmasak3.jpeg'],
            ['name' => 'Jaket Outdoor Waterproof', 'category' => 'Pakaian', 'description' => 'Jaket outdoor berkualitas tinggi dengan teknologi waterproof dan breathable.', 'price' => 450000, 'rent_price' => 35000, 'is_rental' => true, 'stock' => 6, 'image_file' => 'tenda2.jpg'],
            ['name' => 'Tali Rappeling 50m', 'category' => 'Safety', 'description' => 'Tali rappeling profesional dengan standar keamanan internasional.', 'price' => 800000, 'rent_price' => 60000, 'is_rental' => true, 'stock' => 4, 'image_file' => 'tenda3.jpg'],
            ['name' => 'Helm Climbing', 'category' => 'Safety', 'description' => 'Helm climbing dengan perlindungan maksimal untuk keselamatan Anda.', 'price' => 350000, 'rent_price' => 25000, 'is_rental' => true, 'stock' => 9, 'image_file' => 'sepatu1.jpeg'],
            ['name' => 'Binocular Trekking', 'category' => 'Aksesori', 'description' => 'Binocular berkualitas untuk melihat satwa dan pemandangan saat trekking.', 'price' => 600000, 'rent_price' => 45000, 'is_rental' => true, 'stock' => 5, 'image_file' => 'sepatu2.jpeg'],
            ['name' => 'Sepatu Hiking Gore-Tex', 'category' => 'Sepatu', 'description' => 'Sepatu hiking profesional dengan teknologi Gore-Tex, waterproof dan breathable.', 'price' => 750000, 'rent_price' => 55000, 'is_rental' => true, 'stock' => 7, 'image_file' => 'sepatu3.jpeg'],
            ['name' => 'Kompas Trekking', 'category' => 'Navigasi', 'description' => 'Kompas profesional untuk navigasi di hutan dan gunung.', 'price' => 120000, 'rent_price' => 8000, 'is_rental' => true, 'stock' => 15, 'image_file' => 'sleepingbag2.jpeg'],
            ['name' => 'First Aid Kit Outdoor', 'category' => 'Safety', 'description' => 'Kotak P3K lengkap untuk kegiatan outdoor dan darurat.', 'price' => 180000, 'rent_price' => 12000, 'is_rental' => true, 'stock' => 10, 'image_file' => 'sleepingbag3.jpeg'],
            ['name' => 'Trekking Pole', 'category' => 'Aksesori', 'description' => 'Tongkat trekking adjustable untuk membantu perjalanan panjang.', 'price' => 250000, 'rent_price' => 15000, 'is_rental' => true, 'stock' => 12, 'image_file' => 'tasgunung2.jpeg'],
            ['name' => 'Filter Air Portable', 'category' => 'Peralatan', 'description' => 'Filter air portabel untuk mendapatkan air bersih di alam bebas.', 'price' => 400000, 'rent_price' => 25000, 'is_rental' => true, 'stock' => 8, 'image_file' => 'tasgunung3.jpeg'],
        ];

        foreach ($products as $p) {
            $filename = $p['image_file'];

            $productData = [
                'name' => $p['name'],
                'category' => $p['category'],
                'description' => $p['description'],
                'price' => $p['price'],
                'rent_price' => $p['rent_price'],
                'is_rental' => $p['is_rental'],
                'stock' => $p['stock'],
                // store filename only; API will return full URL to /assets/images/
                'image' => $filename,
                'status' => 'approved',
                'user_id' => $seller->id,
            ];

            Product::create($productData);
        }
    }
}