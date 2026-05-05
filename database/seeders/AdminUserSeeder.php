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
        // Create admin user if not exists
        if (!User::where('email', 'admin@campify.com')->exists()) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@campify.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
                'last_login' => now(),
            ]);
        }

        // Create sample buyer user
        if (!User::where('email', 'buyer@campify.com')->exists()) {
            User::create([
                'name' => 'Sample Buyer',
                'email' => 'buyer@campify.com',
                'password' => Hash::make('buyer123'),
                'role' => 'buyer',
                'status' => 'active',
            ]);
        }

        // Create sample seller user
        if (!User::where('email', 'seller@campify.com')->exists()) {
            User::create([
                'name' => 'Sample Seller',
                'email' => 'seller@campify.com',
                'password' => Hash::make('seller123'),
                'role' => 'seller',
                'status' => 'active',
            ]);
        }
    }
}