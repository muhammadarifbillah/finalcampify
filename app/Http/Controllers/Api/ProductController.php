<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private function normalizeImagePath(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $clean = trim($value);
        $clean = parse_url($clean, PHP_URL_PATH) ?: $clean;
        $clean = ltrim($clean, '/');

        if (str_starts_with($clean, 'assets/images/')) {
            $clean = substr($clean, strlen('assets/images/'));
        }

        if (str_starts_with($clean, 'images/')) {
            $clean = substr($clean, strlen('images/'));
        }

        return ltrim($clean, '/');
    }

    private function imageUrl(?string $value): ?string
    {
        $path = $this->normalizeImagePath($value);

        return $path ? url('images/' . $path) : null;
    }

    public function index(Request $request)
    {
        $products = Product::where('status', 'approved');

        if ($request->has('category')) {
            $products->where('category', $request->category);
        }

        if ($request->has('search')) {
            $products->where('name', 'like', '%' . $request->search . '%');
        }

        $items = $products->get()->map(function ($p) {
            $pArr = $p->toArray();

            $img = $pArr['image'] ?? $pArr['gambar'] ?? null;
            $normalizedMainImage = $this->imageUrl($img);
            if ($normalizedMainImage) {
                $pArr['image'] = $normalizedMainImage;
                $pArr['gambar'] = $normalizedMainImage;
            }

            if (isset($pArr['product_images']) && is_array($pArr['product_images'])) {
                $pArr['productImages'] = array_map(function ($im) {
                    if (is_array($im)) {
                        $i = $im['image_url'] ?? $im['image'] ?? null;
                        $normalizedProductImage = $this->imageUrl($i);
                        if ($normalizedProductImage) {
                            $im['image_url'] = $normalizedProductImage;
                        }
                    }
                    return $im;
                }, $pArr['product_images']);
            }

            return $pArr;
        });

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    public function show($id)
    {
        $product = Product::with(['seller', 'category', 'productImages'])->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        // Normalize image fields before returning
        $pArr = $product->toArray();
        $img = $pArr['image'] ?? $pArr['gambar'] ?? null;
        $normalizedMainImage = $this->imageUrl($img);
        if ($normalizedMainImage) {
            $pArr['image'] = $normalizedMainImage;
            $pArr['gambar'] = $normalizedMainImage;
        }

        if (isset($pArr['product_images']) && is_array($pArr['product_images'])) {
            $pArr['productImages'] = array_map(function ($im) {
                if (is_array($im)) {
                    $i = $im['image_url'] ?? $im['image'] ?? null;
                    $normalizedProductImage = $this->imageUrl($i);
                    if ($normalizedProductImage) {
                        $im['image_url'] = $normalizedProductImage;
                    }
                }
                return $im;
            }, $pArr['product_images']);
        }

        return response()->json([
            'success' => true,
            'data' => $pArr
        ]);
    }
}
