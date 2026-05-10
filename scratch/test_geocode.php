<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

function geocode($address) {
    echo "Geocoding: $address\n";
    $response = Illuminate\Support\Facades\Http::withHeaders([
        'User-Agent' => 'CampifyApp/1.0'
    ])->get('https://nominatim.openstreetmap.org/search', [
        'q' => $address,
        'format' => 'json',
        'limit' => 1
    ]);
    
    if ($response->successful() && isset($response->json()[0])) {
        return $response->json()[0];
    }
    return null;
}

$addr1 = "Jl.Lengkong, Bandung";
$addr2 = "Jl. Sukabirus.A1a Citeureup Kec. Dayeuhkolot, Jawa, Barat, Kabupaten Bandung, Jawa Barat";

print_r(geocode($addr1));
print_r(geocode($addr2));
