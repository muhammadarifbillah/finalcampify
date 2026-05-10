<?php

namespace Database\Seeders;

use App\Models\Courier;
use Illuminate\Database\Seeder;

class CourierSeeder extends Seeder
{
    public function run(): void
    {
        $couriers = [
            [
                // Cocok dengan role pembeli (shipping_method: jne)
                'service' => 'jne',
                'name' => 'JNE Express',
                'estimate' => '2-3 hari',
                'price' => 15000,
                'status' => 'aktif',
            ],
            [
                // Cocok dengan role pembeli (shipping_method: gosend)
                'service' => 'gosend',
                'name' => 'GoSend',
                'estimate' => '1 hari',
                'price' => 25000,
                'status' => 'aktif',
            ],
        ];

        foreach ($couriers as $data) {
            Courier::updateOrCreate(
                [
                    'service' => $data['service'],
                    'name' => $data['name'],
                ],
                $data
            );
        }
    }
}

