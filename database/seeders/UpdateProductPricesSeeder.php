<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class UpdateProductPricesSeeder extends Seeder
{
    /**
     * Harga yang sudah dibulatkan dan diturunkan agar lebih wajar.
     * Key = keyword dari nama produk (lowercase), Value = [buy_price, rent_price]
     */
    private array $priceMap = [
        'tenda dome'       => ['buy' => 500000, 'rent' => 20000],
        'sleeping bag'     => ['buy' => 200000, 'rent' => 15000],
        'kompor'           => ['buy' => 120000, 'rent' => 10000],
        'matras'           => ['buy' => 100000, 'rent' => 10000],
        'backpack'         => ['buy' => 400000, 'rent' => 20000],
        'tas'              => ['buy' => 400000, 'rent' => 20000],
        'jaket'            => ['buy' => 250000, 'rent' => 15000],
        'sepatu'           => ['buy' => 350000, 'rent' => 15000],
        'headlamp'         => ['buy' => 75000,  'rent' => 5000],
        'kursi lipat'      => ['buy' => 150000, 'rent' => 10000],
        'trekking pole'    => ['buy' => 150000, 'rent' => 10000],
        'carrier'          => ['buy' => 450000, 'rent' => 25000],
    ];

    public function run(): void
    {
        $products = Product::all();

        foreach ($products as $product) {
            $nameLower = strtolower($product->name ?? '');
            $matched = false;

            foreach ($this->priceMap as $keyword => $prices) {
                if (str_contains($nameLower, $keyword)) {
                    $isRental = $product->jenis_produk === 'sewa' || $product->rent_price > 0;

                    $product->buy_price  = $isRental ? $prices['buy']  : $prices['buy'];
                    $product->rent_price = $isRental ? $prices['rent'] : 0;
                    $product->price      = $isRental ? $prices['rent'] : $prices['buy'];
                    $product->harga      = $product->price;
                    $product->save();

                    $matched = true;
                    break;
                }
            }

            // Fallback: produk yang tidak cocok keyword, bulatkan ke ribuan terdekat ke bawah
            if (!$matched) {
                if ($product->buy_price > 0) {
                    // Turunkan 20% dan bulatkan ke 5000 terdekat
                    $newBuy = floor(($product->buy_price * 0.8) / 5000) * 5000;
                    $product->buy_price = $newBuy;
                    $product->price     = $newBuy;
                    $product->harga     = $newBuy;
                }
                if ($product->rent_price > 0) {
                    $newRent = floor(($product->rent_price * 0.8) / 1000) * 1000;
                    $product->rent_price = $newRent;
                }
                $product->save();
            }
        }

        $this->command->info('✅ Harga produk berhasil diperbarui!');
    }
}
