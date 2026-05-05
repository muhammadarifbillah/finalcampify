<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoreFactory extends Factory
{
    protected $model = Store::class;

    public function definition()
    {
        $statuses = ['pending', 'active', 'rejected', 'suspended', 'banned'];

        return [
            'user_id' => optional(User::inRandomOrder()->first())->id ?? User::factory()->create()->id,
            'nama_toko' => $this->faker->company . ' Store',
            'status' => $this->faker->randomElement($statuses),
            'last_active' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'alasan_ban' => null,
            'deskripsi' => $this->faker->sentence(10),
            'alamat' => $this->faker->address,
            'logo' => 'https://via.placeholder.com/200x200?text=' . urlencode(substr($this->faker->company, 0, 1)),
            'catatan_admin' => null,
        ];
    }
}
