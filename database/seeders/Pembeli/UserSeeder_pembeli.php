<?php

namespace Database\Seeders\Pembeli;

use Illuminate\Database\Seeder;
use App\Models\Pembeli\User_pembeli;
use Illuminate\Support\Facades\Hash;

class UserSeeder_pembeli extends Seeder
{
    public function run(): void
    {
        User_pembeli::create([
            'name' => 'User Demo',
            'email' => 'demo@gmail.com',
            'password' => Hash::make('password')
        ]);
    }
}
