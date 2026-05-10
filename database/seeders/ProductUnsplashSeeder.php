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

        $products = [
            [
                'name' => 'Tenda Dome 2 Orang',
                'category' => 'Tenda',
                'description' => 'Tenda dome berkualitas tinggi cocok untuk 2 orang. Material waterproof dan tahan angin kuat.',
                'price' => 450000,
                'rent_price' => 50000,
                'is_rental' => true,
                'stock' => 10,
                'image' => 'https://source.unsplash.com/600x600/?tent,camping',
                'status' => 'approved',
                'user_id' => $seller->id,
            ],
            [
                'name' => 'Sleeping Bag Premium',
                'category' => 'Sleeping Bag',
                'description' => 'Sleeping bag dengan teknologi thermal, cocok untuk musim dingin dan panas.',
                'price' => 350000,
                'rent_price' => 30000,
                'is_rental' => true,
                'stock' => 15,
                'image' => 'https://source.unsplash.com/600x600/?sleeping-bag,camping',
                'status' => 'approved',
                'user_id' => $seller->id,
            ],
            [
                'name' => 'Backpack 60L',
                'category' => 'Backpack',
                'description' => 'Backpack hiking profesional dengan kapasitas 60 liter, ergonomis dan tahan lama.',
                'price' => 550000,
                'rent_price' => 40000,
                'is_rental' => true,
                'stock' => 8,
                'image' => 'https://source.unsplash.com/600x600/?backpack,hiking',
                'status' => 'approved',
                'user_id' => $seller->id,
            ],
            [
                'name' => 'Matras Camping Inflatable',
                'category' => 'Matras',
                'description' => 'Matras camping yang dapat dikembung, ringan dan nyaman untuk tidur outdoor.',
                'price' => 200000,
                'rent_price' => 15000,
                'is_rental' => true,
                'stock' => 12,
                'image' => 'https://source.unsplash.com/600x600/?air-mattress,camping',
                'status' => 'approved',
                'user_id' => $seller->id,
            ],
            [
                'name' => 'Kompor Portable',
                'category' => 'Peralatan Masak',
                'description' => 'Kompor portable gas untuk memasak di alam bebas, ringan dan praktis.',
                'price' => 150000,
                'rent_price' => 12000,
                'is_rental' => true,
                'stock' => 20,
                'image' => 'https://source.unsplash.com/600x600/?camping-stove,outdoor',
                'status' => 'approved',
                'user_id' => $seller->id,
            ],
            [
                'name' => 'Lampu LED Camping',
                'category' => 'Pencahayaan',
                'description' => 'Lampu LED multi-fungsi untuk camping, hemat energi dan terang maksimal.',
                'price' => 100000,
                'rent_price' => 8000,
                'is_rental' => true,
                'stock' => 25,
                'image' => 'https://source.unsplash.com/600x600/?camping-lantern,led-light',
                'status' => 'approved',
                'user_id' => $seller->id,
            ],
            [
                'name' => 'Jaket Outdoor Waterproof',
                'category' => 'Pakaian',
                'description' => 'Jaket outdoor berkualitas tinggi dengan teknologi waterproof dan breathable.',
                'price' => 450000,
                'rent_price' => 35000,
                'is_rental' => true,
                'stock' => 6,
                'image' => 'https://source.unsplash.com/600x600/?jacket,outdoor',
                'status' => 'approved',
                'user_id' => $seller->id,
            ],
            [
                'name' => 'Tali Rappeling 50m',
                'category' => 'Safety',
                'description' => 'Tali rappeling profesional dengan standar keamanan internasional.',
                'price' => 800000,
                'rent_price' => 60000,
                'is_rental' => true,
                'stock' => 4,
                'image' => 'https://source.unsplash.com/600x600/?climbing-rope,mountain',
                'status' => 'approved',
                'user_id' => $seller->id,
            ],
            [
                'name' => 'Helm Climbing',
                'category' => 'Safety',
                'description' => 'Helm climbing dengan perlindungan maksimal untuk keselamatan Anda.',
                'price' => 350000,
                'rent_price' => 25000,
                'is_rental' => true,
                'stock' => 9,
                'image' => 'https://source.unsplash.com/600x600/?climbing-helmet,mountain',
                'status' => 'approved',
                'user_id' => $seller->id,
            ],
            [
                'name' => 'Binocular Trekking',
                'category' => 'Aksesori',
                'description' => 'Binocular berkualitas untuk melihat satwa dan pemandangan saat trekking.',
                'price' => 600000,
                'rent_price' => 45000,
                'is_rental' => true,
                'stock' => 5,
                'image' => 'https://source.unsplash.com/600x600/?binoculars,nature',
                'status' => 'approved',
                'user_id' => $seller->id,
            ],
            [
                'name' => 'Sepatu Hiking Gore-Tex',
                'category' => 'Sepatu',
                'description' => 'Sepatu hiking profesional dengan teknologi Gore-Tex, waterproof dan breathable.',
                'price' => 750000,
                'rent_price' => 55000,
                'is_rental' => true,
                'stock' => 7,
                'image' => 'https://source.unsplash.com/600x600/?hiking-boots,outdoor',
                'status' => 'approved',
                'user_id' => $seller->id,
            ],
            [
                'name' => 'Kompas Trekking',
                'category' => 'Navigasi',
                'description' => 'Kompas profesional untuk navigasi di hutan dan gunung.',
                'price' => 120000,
                'rent_price' => 8000,
                'is_rental' => true,
                'stock' => 15,
                'image' => 'https://source.unsplash.com/600x600/?compass,navigation',
                'status' => 'approved',
                'user_id' => $seller->id,
            ],
            [
                'name' => 'First Aid Kit Outdoor',
                'category' => 'Safety',
                'description' => 'Kotak P3K lengkap untuk kegiatan outdoor dan darurat.',
                'price' => 180000,
                'rent_price' => 12000,
                'is_rental' => true,
                'stock' => 10,
                'image' => 'https://source.unsplash.com/600x600/?first-aid,medical',
                'status' => 'approved',
                'user_id' => $seller->id,
            ],
            [
                'name' => 'Trekking Pole',
                'category' => 'Aksesori',
                'description' => 'Tongkat trekking adjustable untuk membantu perjalanan panjang.',
                'price' => 250000,
                'rent_price' => 15000,
                'is_rental' => true,
                'stock' => 12,
                'image' => 'https://source.unsplash.com/600x600/?trekking-poles,hiking',
                'status' => 'approved',
                'user_id' => $seller->id,
            ],
            [
                'name' => 'Filter Air Portable',
                'category' => 'Peralatan',
                'description' => 'Filter air portabel untuk mendapatkan air bersih di alam bebas.',
                'price' => 400000,
                'rent_price' => 25000,
                'is_rental' => true,
                'stock' => 8,
                'image' => 'https://source.unsplash.com/600x600/?water-filter,outdoor',
                'status' => 'approved',
                'user_id' => $seller->id,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}