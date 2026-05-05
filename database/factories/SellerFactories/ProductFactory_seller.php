<?php

namespace Database\Factories\SellerFactories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory_seller extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => 1,
            'nama_produk' => $this->faker->word(),
            'deskripsi' => $this->faker->sentence(),
            'harga' => $this->faker->numberBetween(10000, 500000),
            'kategori' => $this->faker->randomElement(['jual', 'sewa']),
            'gambar' => null
        ];
    }
}


