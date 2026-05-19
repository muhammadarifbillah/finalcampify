<?php
include 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$products = DB::table('products')->select('id', 'name', 'buy_price', 'rent_price', 'jenis_produk', 'is_rental', 'status')->get();
foreach ($products as $p) {
    echo "ID: {$p->id} | Name: {$p->name} | Buy Price: {$p->buy_price} | Rent Price: {$p->rent_price} | Jenis: {$p->jenis_produk} | Is Rental: {$p->is_rental} | Status: {$p->status}\n";
}
