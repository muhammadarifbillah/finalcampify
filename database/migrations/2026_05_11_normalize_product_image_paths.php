<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create assets/images directory if it doesn't exist
        if (!is_dir(public_path('assets/images'))) {
            mkdir(public_path('assets/images'), 0755, true);
        }

        // Get all products with image paths
        $products = \DB::table('products')
            ->whereNotNull('gambar')
            ->orWhereNotNull('image')
            ->get();

        foreach ($products as $product) {
            $imagePath = $product->gambar ?: $product->image;
            
            if (!$imagePath) {
                continue;
            }

            // If it's already just a filename (new format), skip
            if (!strpos($imagePath, '/') && !strpos($imagePath, '\\')) {
                continue;
            }

            // Extract filename from path
            $filename = basename($imagePath);
            $oldPath = null;

            // Try to find the old file in storage
            if (strpos($imagePath, 'products/') !== false) {
                $oldPath = storage_path('app/public/' . $imagePath);
            } elseif (strpos($imagePath, 'ktp_uploads/') !== false) {
                $oldPath = storage_path('app/public/' . $imagePath);
            } elseif (strpos($imagePath, 'returns') !== false) {
                $oldPath = storage_path('app/public/' . $imagePath);
            }

            // Copy file to new location if old file exists
            if ($oldPath && file_exists($oldPath)) {
                $newPath = public_path('assets/images/' . $filename);
                if (!file_exists($newPath)) {
                    copy($oldPath, $newPath);
                }
            }

            // Update database to store only filename
            \DB::table('products')
                ->where('id', $product->id)
                ->update([
                    'gambar' => $filename,
                    'image' => $filename,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally, you could restore old paths, but it's not recommended
        // as the new system should be the source of truth
    }
};
