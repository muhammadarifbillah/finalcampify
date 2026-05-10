<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Store;

// Hapus produk lama agar bersih sesuai permintaan "disesuaikan jumlahnya"
\Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
Product::truncate();
\Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

$images = [
    ['file' => 'assets/images/alatmasak1.jpeg', 'category' => 'Alat Masak', 'name' => 'Kompor Portable Ultralight'],
    ['file' => 'assets/images/alatmasak2.jpeg', 'category' => 'Alat Masak', 'name' => 'Cooking Set Nesting DS-300'],
    ['file' => 'assets/images/alatmasak3.jpeg', 'category' => 'Alat Masak', 'name' => 'Windshield Kompor Camping'],
    ['file' => 'assets/images/sepatu1.jpeg', 'category' => 'Sepatu', 'name' => 'Sepatu Gunung Hiking Waterproof'],
    ['file' => 'assets/images/sepatu2.jpeg', 'category' => 'Sepatu', 'name' => 'Sepatu Trail Running Outdoor'],
    ['file' => 'assets/images/sepatu3.jpeg', 'category' => 'Sepatu', 'name' => 'Sepatu Safety Outdoor Pro'],
    ['file' => 'assets/images/sleepingbag1.jpeg', 'category' => 'Sleeping Bag', 'name' => 'Sleeping Bag Polar Bulu'],
    ['file' => 'assets/images/sleepingbag2.jpeg', 'category' => 'Sleeping Bag', 'name' => 'Sleeping Bag Dacron Cabin'],
    ['file' => 'assets/images/sleepingbag3.jpeg', 'category' => 'Sleeping Bag', 'name' => 'Sleeping Bag Mummy Extreme'],
    ['file' => 'assets/images/tasgunung1.jpeg', 'category' => 'Tas Gunung', 'name' => 'Carrier 60L Adventure'],
    ['file' => 'assets/images/tasgunung2.jpeg', 'category' => 'Tas Gunung', 'name' => 'Daypack 30L Commuter'],
    ['file' => 'assets/images/tasgunung3.jpeg', 'category' => 'Tas Gunung', 'name' => 'Carrier 80L Expedition'],
    ['file' => 'assets/images/tenda1.jpg', 'category' => 'Tenda', 'name' => 'Tenda Dome 4 Orang'],
    ['file' => 'assets/images/tenda2.jpg', 'category' => 'Tenda', 'name' => 'Tenda Ultralight 2 Orang'],
    ['file' => 'assets/images/tenda3.jpg', 'category' => 'Tenda', 'name' => 'Tenda Kapasitas 6 Orang Premium'],
];

$stores = Store::all();
if ($stores->isEmpty()) {
    echo "Tidak ada toko ditemukan!\n";
    exit;
}

// Buat Produk Jual (15)
foreach ($images as $index => $img) {
    $store = $stores[$index % $stores->count()];
    Product::create([
        'store_id' => $store->id,
        'user_id' => $store->user_id,
        'name' => $img['name'],
        'nama_produk' => $img['name'],
        'category' => $img['category'],
        'kategori' => $img['category'],
        'description' => 'Peralatan berkualitas tinggi untuk kebutuhan camping Anda.',
        'deskripsi' => 'Peralatan berkualitas tinggi untuk kebutuhan camping Anda.',
        'buy_price' => rand(150000, 2500000),
        'rent_price' => 0,
        'price' => rand(150000, 2500000),
        'harga' => rand(150000, 2500000),
        'jenis_produk' => 'jual',
        'is_rental' => 0,
        'status' => 'approved',
        'image' => $img['file'],
        'gambar' => $img['file'],
        'stock' => rand(5, 20),
        'stok' => rand(5, 20),
        'rating' => 4.5,
        'reviews_count' => rand(10, 100),
    ]);
}

// Buat Produk Sewa (15)
foreach ($images as $index => $img) {
    $store = $stores[($index + 1) % $stores->count()];
    Product::create([
        'store_id' => $store->id,
        'user_id' => $store->user_id,
        'name' => $img['name'] . ' (Sewa)',
        'nama_produk' => $img['name'] . ' (Sewa)',
        'category' => $img['category'],
        'kategori' => $img['category'],
        'description' => 'Sewa peralatan camping murah dan terawat.',
        'deskripsi' => 'Sewa peralatan camping murah dan terawat.',
        'buy_price' => rand(500000, 3000000), // Digunakan sebagai nilai jaminan
        'rent_price' => rand(15000, 150000),
        'price' => rand(15000, 150000),
        'harga' => rand(15000, 150000),
        'jenis_produk' => 'sewa',
        'is_rental' => 1,
        'status' => 'approved',
        'image' => $img['file'],
        'gambar' => $img['file'],
        'stock' => rand(2, 10),
        'stok' => rand(2, 10),
        'rating' => 4.8,
        'reviews_count' => rand(5, 50),
    ]);
}

echo "Berhasil memperbarui data produk (15 Jual, 15 Sewa) menggunakan gambar lokal.\n";
