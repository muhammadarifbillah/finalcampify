<?php
namespace Database\Factories\Pembeli;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pembeli\Product_pembeli;

class ProductFactory_pembeli extends Factory
{
    protected $model = Product_pembeli::class;
    public function definition()
    {
        return [
            'name' => $this->faker->words(2, true),
            'category' => $this->faker->randomElement(['Elektronik', 'Pakaian', 'Alat Rumah Tangga']),
            'description' => $this->faker->sentence(),
            'buy_price' => $this->faker->numberBetween(50000, 500000),
            'rent_price' => $this->faker->numberBetween(5000, 50000),
            'image' => 'default.jpg',
            'stock' => $this->faker->numberBetween(1, 100),
        ];
    }
}
