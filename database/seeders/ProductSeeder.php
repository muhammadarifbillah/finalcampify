<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
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
                'image' => 'https://via.placeholder.com/300x300?text=Tenda+Dome',
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
                'image' => 'https://via.placeholder.com/300x300?text=Sleeping+Bag',
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
                'image' => 'https://via.placeholder.com/300x300?text=Backpack+60L',
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
                'image' => 'https://via.placeholder.com/300x300?text=Matras+Inflatable',
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
                'image' => 'https://via.placeholder.com/300x300?text=Kompor+Portable',
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
                'image' => 'https://via.placeholder.com/300x300?text=Lampu+LED',
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
                'image' => 'https://via.placeholder.com/300x300?text=Jaket+Outdoor',
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
                'image' => 'https://via.placeholder.com/300x300?text=Tali+Rappeling',
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
                'image' => 'https://via.placeholder.com/300x300?text=Helm+Climbing',
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
                'image' => 'https://via.placeholder.com/300x300?text=Binocular',
                'status' => 'approved',
                'user_id' => $seller->id,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
