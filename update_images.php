<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pembeli\Product_pembeli;
use Illuminate\Support\Facades\File;

$artifactsDir = 'C:\Users\Arif\.gemini\antigravity\brain\ff2c2644-3612-41ce-be29-6bac29edacbe\\';
$publicProductsDir = public_path('storage/products');

if (!File::exists($publicProductsDir)) {
    File::makeDirectory($publicProductsDir, 0755, true);
}

// Images we generated
$generated = [
    'Trekking Pole (Sepasang)' => 'trekking_pole_1778419773583.png',
    'Kursi Lipat Camping' => 'kursi_lipat_1778419824044.png',
    'Kompor Portable Camping' => 'kompor_portable_1778419893363.png',
    'Sleeping Bag Thermal' => 'sleeping_bag_1778419908032.png',
    'Headlamp LED Rechargeable' => 'headlamp_1778419997892.png',
    'Matras Camping' => 'matras_camping_1778420058435.png',
];

// Fallbacks for the ones that failed rate limit (we download from Unsplash)
$fallbacks = [
    'Sepatu Hiking Trail' => 'https://images.unsplash.com/photo-1596700676451-246e7f8efad9?w=800&q=80',
    'Backpack Hiking 60L' => 'https://images.unsplash.com/photo-1622260614153-03223fb72052?w=800&q=80',
    'Tenda Dome 2 Orang' => 'https://images.unsplash.com/photo-1504280327335-584ea07cb113?w=800&q=80',
];

$products = Product_pembeli::all();

foreach ($products as $product) {
    $name = $product->name;
    $filename = strtolower(str_replace(' ', '_', preg_replace('/[^a-zA-Z0-9 ]/', '', $name))) . '_' . uniqid() . '.jpg';
    $destPath = $publicProductsDir . '/' . $filename;
    $dbPath = 'products/' . $filename;
    
    $success = false;

    if (isset($generated[$name])) {
        // Copy from artifacts
        $sourcePath = $artifactsDir . $generated[$name];
        if (File::exists($sourcePath)) {
            File::copy($sourcePath, $destPath);
            $success = true;
            echo "Copied AI image for {$name}\n";
        } else {
            echo "Failed to find AI image: {$sourcePath}\n";
        }
    } else if (isset($fallbacks[$name])) {
        // Download from Unsplash
        $imageContent = @file_get_contents($fallbacks[$name]);
        if ($imageContent) {
            File::put($destPath, $imageContent);
            $success = true;
            echo "Downloaded Unsplash image for {$name}\n";
        } else {
            echo "Failed to download image for {$name}\n";
        }
    }

    if ($success) {
        $product->image = $dbPath;
        $product->save();
    }
}

echo "Image update complete!\n";
