<?php

namespace Database\Seeders\Pembeli;

use Illuminate\Database\Seeder;
use App\Models\Pembeli\Product_pembeli;

class ProductSeeder_pembeli extends Seeder
{
    public function run(): void
    {
        // Clear existing products and related data
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Product_pembeli::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Buy-only products
        Product_pembeli::create([
            'name' => 'Guardian 8P Tent',
            'category' => 'Tenda',
            'description' => 'Tenda kapasitas 4 orang...',
            'buy_price' => 4500000,
            'rent_price' => null,
            'rating' => 4.8,
            'reviews_count' => 124,
            'image' => 'storage/uploads/tendacamping1.png',
            'stock' => 10
        ]);

        Product_pembeli::create([
            'name' => 'Consina Cloud Sleeping Bag',
            'category' => 'Sleeping Bag',
            'description' => 'Sleeping bag hangat...',
            'buy_price' => 350000,
            'rent_price' => null,
            'rating' => 4.5,
            'reviews_count' => 89,
            'image' => 'storage/uploads/sleepingbag.jpeg',
            'stock' => 25
        ]);

        Product_pembeli::create([
            'name' => 'Primus Camping Stove',
            'category' => 'Alat Masak',
            'description' => 'Kompor camping portabel...',
            'buy_price' => 250000,
            'rent_price' => null,
            'rating' => 4.7,
            'reviews_count' => 156,
            'image' => 'storage/uploads/kompor.jpeg',
            'stock' => 15
        ]);

        Product_pembeli::create([
            'name' => 'The North Face Borealis Backpack',
            'category' => 'Tas Gunung',
            'description' => 'Tas gunung kapasitas 65L...',
            'buy_price' => 1200000,
            'rent_price' => null,
            'rating' => 4.9,
            'reviews_count' => 203,
            'image' => 'storage/uploads/tas.jpeg',
            'stock' => 8
        ]);

        Product_pembeli::create([
            'name' => 'Camping Mug Stainless Steel',
            'category' => 'Aksesoris',
            'description' => 'Mug stainless steel untuk camping...',
            'buy_price' => 50000,
            'rent_price' => null,
            'rating' => 4.3,
            'reviews_count' => 45,
            'image' => 'storage/uploads/Camping Mug Stainless Steel.jpg',
            'stock' => 30
        ]);

        Product_pembeli::create([
            'name' => 'LED Camping Lantern',
            'category' => 'Aksesoris',
            'description' => 'Lampu LED portabel untuk camping...',
            'buy_price' => 75000,
            'rent_price' => null,
            'rating' => 4.6,
            'reviews_count' => 67,
            'image' => 'storage/uploads/headlamp2.jpeg',
            'stock' => 25
        ]);

        Product_pembeli::create([
            'name' => 'Multi-tool Survival Kit',
            'category' => 'Aksesoris',
            'description' => 'Kit alat multifungsi untuk survival...',
            'buy_price' => 150000,
            'rent_price' => null,
            'rating' => 4.8,
            'reviews_count' => 89,
            'image' => 'storage/uploads/survival kit.jpeg',
            'stock' => 18
        ]);

        // Rent-only products
        Product_pembeli::create([
            'name' => 'Black Diamond Trekking Poles',
            'category' => 'Aksesoris',
            'description' => 'Tongkat trekking adjustable...',
            'buy_price' => null,
            'rent_price' => 25000,
            'rating' => 4.6,
            'reviews_count' => 78,
            'image' => 'storage/uploads/trekking.jpeg',
            'stock' => 20
        ]);

        Product_pembeli::create([
            'name' => 'MSR Water Filter',
            'category' => 'Aksesoris',
            'description' => 'Filter air portabel...',
            'buy_price' => null,
            'rent_price' => 30000,
            'rating' => 4.4,
            'reviews_count' => 92,
            'image' => 'storage/uploads/msr-water-filters.jpg',
            'stock' => 12
        ]);

        Product_pembeli::create([
            'name' => 'Professional DSLR Camera',
            'category' => 'Kamera',
            'description' => 'Kamera DSLR profesional untuk fotografi...',
            'buy_price' => null,
            'rent_price' => 100000,
            'rating' => 4.9,
            'reviews_count' => 145,
            'image' => 'storage/uploads/canon-product-shot-600x475.jpg',
            'stock' => 5
        ]);

        Product_pembeli::create([
            'name' => 'Video Drone with 4K Camera',
            'category' => 'Drone',
            'description' => 'Drone video dengan kamera 4K...',
            'buy_price' => null,
            'rent_price' => 200000,
            'rating' => 4.7,
            'reviews_count' => 98,
            'image' => 'storage/uploads/camera.jpg',
            'stock' => 3
        ]);

        Product_pembeli::create([
            'name' => 'Matras Tidur',
            'category' => 'Aksesoris',
            'description' => 'Matras tidur untuk camping...',
            'buy_price' => null,
            'rent_price' => 75000,
            'rating' => 4.5,
            'reviews_count' => 76,
            'image' => 'storage/uploads/matras camping.webp',
            'stock' => 8
        ]);
    }
}
