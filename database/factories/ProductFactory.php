<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        $status = ['pending', 'approved', 'rejected'];
        // Outdoor activity products
        $outdoorProducts = [
            ['name' => 'Tenda Camping 2 Orang', 'category' => 'Tenda', 'image' => 'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?w=600&h=400&fit=crop'],
            ['name' => 'Sleeping Bag Thermal', 'category' => 'Sleeping Bag', 'image' => 'https://images.unsplash.com/photo-1594735707802-cd94cf889e3e?w=600&h=400&fit=crop'],
            ['name' => 'Backpack Hiking 60L', 'category' => 'Backpack', 'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=600&h=400&fit=crop'],
            ['name' => 'Sepeda Gunung MTB', 'category' => 'Sepeda', 'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&h=400&fit=crop'],
            ['name' => 'Jaket Outdoor Waterproof', 'category' => 'Pakaian', 'image' => 'https://images.unsplash.com/photo-1544448871-58fb46c8a0eb?w=600&h=400&fit=crop'],
            ['name' => 'Sepatu Hiking Professional', 'category' => 'Sepatu', 'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&h=400&fit=crop'],
            ['name' => 'Kompas dan Peta Digital', 'category' => 'Navigation', 'image' => 'https://images.unsplash.com/photo-1569163139394-de4798aa62b1?w=600&h=400&fit=crop'],
            ['name' => 'Headlamp LED Rechargeable', 'category' => 'Lighting', 'image' => 'https://images.unsplash.com/photo-1606986628025-35d57edb6f5f?w=600&h=400&fit=crop'],
            ['name' => 'Matras Camping Foam', 'category' => 'Matras', 'image' => 'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?w=600&h=400&fit=crop'],
            ['name' => 'Carabiner dan Rope Set', 'category' => 'Safety', 'image' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=600&h=400&fit=crop'],
            ['name' => 'Portable Camping Stove', 'category' => 'Cookware', 'image' => 'https://images.unsplash.com/photo-1554080221-a5b5b94a01b8?w=600&h=400&fit=crop'],
            ['name' => 'Water Bottle Stainless Steel 1L', 'category' => 'Accessories', 'image' => 'https://images.unsplash.com/photo-1602143407151-7e36dd5f5a0e?w=600&h=400&fit=crop'],
            ['name' => 'Tent Footprint and Repair Kit', 'category' => 'Tenda', 'image' => 'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?w=600&h=400&fit=crop'],
            ['name' => 'Multi-tool Pocket Knife', 'category' => 'Tools', 'image' => 'https://images.unsplash.com/photo-1609042231885-609ae6602d2a?w=600&h=400&fit=crop'],
            ['name' => 'Drone untuk Fotografi Outdoor', 'category' => 'Photography', 'image' => 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=600&h=400&fit=crop'],
            ['name' => 'Kamera Action GoPro', 'category' => 'Photography', 'image' => 'https://images.unsplash.com/photo-1611532736579-6b16e2b50449?w=600&h=400&fit=crop'],
            ['name' => 'Thermal Underwear Set', 'category' => 'Pakaian', 'image' => 'https://images.unsplash.com/photo-1544448871-58fb46c8a0eb?w=600&h=400&fit=crop'],
            ['name' => 'Climbing Rope 50m', 'category' => 'Safety', 'image' => 'https://images.unsplash.com/photo-1544448871-58fb46c8a0eb?w=600&h=400&fit=crop'],
            ['name' => 'Fishing Rod and Tackle Set', 'category' => 'Fishing', 'image' => 'https://images.unsplash.com/photo-1543269865-cbdf26ce6c3f?w=600&h=400&fit=crop'],
            ['name' => 'Binoculars Wildlife Viewing', 'category' => 'Optics', 'image' => 'https://images.unsplash.com/photo-1613842813886-142f9ef6e9e3?w=600&h=400&fit=crop'],
        ];

        $product = $this->faker->randomElement($outdoorProducts);
        $buyPrice = $this->faker->numberBetween(50000, 2500000);
        $rentPrice = $this->faker->boolean(60) ? $this->faker->numberBetween(10000, max(50000, intval($buyPrice * 0.3))) : 0;

        return [
            'store_id' => null,
            'name' => $product['name'],
            'category' => $product['category'],
            'description' => 'Produk outdoor berkualitas tinggi untuk petualangan Anda. Bahan premium, tahan lama, dan dirancang untuk kondisi ekstrem. ' . $this->faker->sentence(8),
            'buy_price' => $buyPrice,
            'rent_price' => $rentPrice,
            'price' => $buyPrice,
            'status' => $this->faker->randomElement($status),
            'is_rental' => $rentPrice > 0,
            'rating' => $this->faker->randomFloat(1, 3.5, 5),
            'reviews_count' => $this->faker->numberBetween(2, 150),
            'image' => $product['image'],
            'stock' => $this->faker->numberBetween(1, 80),
            'created_at' => now()->subDays($this->faker->numberBetween(0, 30)),
            'updated_at' => now(),
        ];
    }
}
