<?php

namespace Database\Factories;

use App\Models\Courier;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourierFactory extends Factory
{
    protected $model = Courier::class;

    public function definition()
    {
        $services = ['JNE', 'TIKI', 'SiCepat', 'Anteraja', 'J&T Express', 'GoSend', 'GrabExpress'];
        $status = ['aktif', 'nonaktif'];

        return [
            'name' => $this->faker->company(),
            'service' => $this->faker->randomElement($services),
            'estimate' => $this->faker->randomElement(['1-2 hari', '2-3 hari', '3-5 hari']),
            'price' => $this->faker->numberBetween(10000, 35000),
            'status' => $this->faker->randomElement($status),
        ];
    }
}
