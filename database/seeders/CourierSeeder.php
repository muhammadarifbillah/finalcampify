<?php

namespace Database\Seeders;

use App\Models\Courier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CourierSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('product_courier')->truncate();
        Courier::truncate();
        Schema::enableForeignKeyConstraints();

        // name  = nama pegawai/driver kurir (bukan user aplikasi)
        // service = nama perusahaan jasa pengiriman
        $couriers = [
            ['service' => 'JNE',        'name' => 'Supriyanto',     'estimate' => '2-3 hari', 'price' => 18000, 'status' => 'aktif'],
            ['service' => 'TIKI',        'name' => 'Wahyu Nugroho',  'estimate' => '2-3 hari', 'price' => 16000, 'status' => 'aktif'],
            ['service' => 'SiCepat',     'name' => 'Tri Mulyono',    'estimate' => '2-3 hari', 'price' => 14000, 'status' => 'aktif'],
            ['service' => 'Anteraja',    'name' => 'Juminten',       'estimate' => '2-3 hari', 'price' => 13000, 'status' => 'aktif'],
            ['service' => 'J&T Express', 'name' => 'Paiman Susilo',  'estimate' => '2-3 hari', 'price' => 15000, 'status' => 'aktif'],
            ['service' => 'GoSend',      'name' => 'Sarwono',        'estimate' => '1 hari',   'price' => 20000, 'status' => 'aktif'],
            ['service' => 'GrabExpress', 'name' => 'Tugimin',        'estimate' => '1 hari',   'price' => 20000, 'status' => 'aktif'],
        ];

        foreach ($couriers as $data) {
            Courier::create($data);
        }
    }
}
