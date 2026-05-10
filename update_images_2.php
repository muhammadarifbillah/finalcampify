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

// Fallbacks for the ones that failed
$fallbacks = [
    'Sepatu Hiking Trail' => 'https://images.unsplash.com/photo-1542220462-8178a9cba2de?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
    'Tenda Dome 2 Orang' => 'https://images.unsplash.com/photo-1525811902636-0c87a15156c8?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
];

$products = Product_pembeli::whereIn('name', array_keys($fallbacks))->get();

$context = stream_context_create([
    'http' => [
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n"
    ]
]);

foreach ($products as $product) {
    $name = $product->name;
    $filename = strtolower(str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9 ]/', '', $name))) . '_' . uniqid() . '.jpg';
    $destPath = $publicProductsDir . '/' . $filename;
    $dbPath = 'products/' . $filename;
    
    $imageContent = @file_get_contents($fallbacks[$name], false, $context);
    if ($imageContent) {
        File::put($destPath, $imageContent);
        $product->image = $dbPath;
        $product->save();
        echo "Downloaded Unsplash image for {$name}\n";
    } else {
        echo "Failed to download image for {$name}\n";
    }
}

echo "Image update complete!\n";
