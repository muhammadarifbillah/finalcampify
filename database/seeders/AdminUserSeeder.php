<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@campify.com'],
            [
                'name' => 'Administrator',
                'nama' => 'Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
                'last_login' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'buyer@campify.com'],
            [
                'name' => 'Sample Buyer',
                'nama' => 'Sample Buyer',
                'password' => Hash::make('buyer123'),
                'role' => 'buyer',
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'seller@campify.com'],
            [
                'name' => 'Sample Seller',
                'nama' => 'Sample Seller',
                'password' => Hash::make('seller123'),
                'role' => 'seller',
                'status' => 'active',
            ]
        );
    }
}
