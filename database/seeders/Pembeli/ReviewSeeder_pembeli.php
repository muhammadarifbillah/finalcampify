<?php

namespace Database\Seeders\Pembeli;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder_pembeli extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Pembeli\ProductRating_pembeli::create([
            'user_id' => 1,
            'produk_id' => 1,
            'rating' => 5,
            'comment' => 'Tenda sangat bagus dan kokoh. Sudah digunakan untuk camping beberapa kali dan tidak ada masalah.',
        ]);

        \App\Models\Pembeli\ProductRating_pembeli::create([
            'user_id' => 1,
            'produk_id' => 2,
            'rating' => 4,
            'comment' => 'Sleeping bag hangat dan nyaman. Harga terjangkau untuk kualitas seperti ini.',
        ]);

        \App\Models\Pembeli\ProductRating_pembeli::create([
            'user_id' => 1,
            'produk_id' => 3,
            'rating' => 5,
            'comment' => 'Kompor camping ini sangat praktis dan mudah digunakan. Api stabil dan tidak mudah padam.',
        ]);
    }
}
