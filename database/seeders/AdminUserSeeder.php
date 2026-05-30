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
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
                'last_login' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'arif@campify.com'],
            [
                'name' => 'Arif Admin',
                'nama' => 'Arif Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
                'last_login' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'rina.safitri@gmail.com'],
            [
                'name' => 'Rina Safitri',
                'password' => Hash::make('buyer123'),
                'role' => 'buyer',
                'status' => 'active',
                'ktp_image' => 'ktp_22_1779214919.png',
                'ktp_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'bagus.outdoor@gmail.com'],
            [
                'name' => 'Bagus Prasetyo',
                'password' => Hash::make('seller123'),
                'role' => 'seller',
                'status' => 'active',
            ]
        );
    }
}