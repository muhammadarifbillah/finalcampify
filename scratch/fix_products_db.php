<?php
include 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$affected = DB::table('products')
    ->where('is_rental', 1)
    ->where(function($query) {
        $query->where('jenis_produk', '!=', 'sewa')
              ->orWhereNull('jenis_produk')
              ->orWhere('buy_price', 0)
              ->orWhereNull('buy_price');
    })
    ->update([
        'jenis_produk' => 'sewa',
        'buy_price' => DB::raw('price')
    ]);

echo "Successfully updated {$affected} mismatched rental products in the database!\n";

// Print updated products to verify
$products = DB::table('products')
    ->select('id', 'name', 'buy_price', 'rent_price', 'jenis_produk', 'is_rental')
    ->where('is_rental', 1)
    ->get();

foreach ($products as $p) {
    echo "ID: {$p->id} | Name: {$p->name} | Buy Price: {$p->buy_price} | Rent Price: {$p->rent_price} | Jenis: {$p->jenis_produk} | Is Rental: {$p->is_rental}\n";
}
