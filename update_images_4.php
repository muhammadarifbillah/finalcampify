<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pembeli\Product_pembeli;
use Illuminate\Support\Facades\File;

$publicProductsDir = public_path('storage/products');

if (!File::exists($publicProductsDir)) {
    File::makeDirectory($publicProductsDir, 0755, true);
}

$fallbacks = [
    'Sepatu Hiking Trail' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4e/Hiking_boots.jpg/800px-Hiking_boots.jpg',
    'Tenda Dome 2 Orang' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/cd/Tents_by_mountain_lake.jpg/800px-Tents_by_mountain_lake.jpg',
];

$products = Product_pembeli::whereIn('name', array_keys($fallbacks))->get();

function downloadImage($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode == 200) {
        return $data;
    }
    return false;
}

foreach ($products as $product) {
    $name = $product->name;
    $filename = strtolower(str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9 ]/', '', $name))) . '_' . uniqid() . '.jpg';
    $destPath = $publicProductsDir . '/' . $filename;
    $dbPath = 'products/' . $filename;
    
    $imageContent = downloadImage($fallbacks[$name]);
    if ($imageContent) {
        File::put($destPath, $imageContent);
        $product->image = $dbPath;
        $product->save();
        echo "Downloaded Wikimedia image for {$name}\n";
    } else {
        echo "Failed to download image for {$name}\n";
    }
}

echo "Image update complete!\n";
