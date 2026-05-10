<?php

namespace App\Http\Controllers\SellerController;

use App\Http\Controllers\Controller;
use App\Models\SellerModels\Order_seller;
use App\Models\SellerModels\Rental_seller;
use App\Models\SellerModels\Product_seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController_seller extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | HALAMAN LAPORAN PENJUALAN (PEMBELIAN)
    |--------------------------------------------------------------------------
    */
    public function salesReport(Request $request)
    {
        $sellerId = Auth::id();

        // Filter tanggal
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Ambil orders yang sudah selesai
        $orders = Order_seller::where('status', 'selesai')
            ->whereHas('details.product', function($query) use ($sellerId) {
                $query->where('user_id', $sellerId);
            })
            ->with(['buyer', 'details.product'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->latest()
            ->get();

        // Hitung total penjualan
        $totalSales = $orders->sum('total_harga');
        $totalOrders = $orders->count();

        // Produk terlaris
        $topProducts = collect();
        foreach ($orders as $order) {
            foreach ($order->details as $detail) {
                $product = $detail->product;
                if ($product) {
                    $existing = $topProducts->firstWhere('id', $product->id);
                    if ($existing) {
                        $existing['quantity'] += $detail->qty;
                        $existing['total'] += $detail->harga * $detail->qty;
                    } else {
                        $topProducts->push([
                            'id' => $product->id,
                            'nama_produk' => $product->nama_produk,
                            'quantity' => $detail->qty,
                            'total' => $detail->harga * $detail->qty,
                        ]);
                    }
                }
            }
        }
        $topProducts = $topProducts->sortByDesc('quantity')->take(5);

        return view('SellerView.reports.sales', compact(
            'orders',
            'totalSales',
            'totalOrders',
            'topProducts',
            'startDate',
            'endDate'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | HALAMAN LAPORAN PENYEWAAN
    |--------------------------------------------------------------------------
    */
    public function rentalReport(Request $request)
    {
        $sellerId = Auth::id();

        // Filter tanggal
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Ambil rentals yang sudah selesai
        $rentals = Rental_seller::where('status', 'selesai')
            ->whereHas('product', function($query) use ($sellerId) {
                $query->where('user_id', $sellerId);
            })
            ->with(['user', 'product'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->latest()
            ->get();

        // Hitung total pendapatan sewa
        $totalRentalIncome = $rentals->sum('price');
        $totalRentals = $rentals->count();

        // Produk tersewa terbanyak
        $topRentedProducts = collect();
        foreach ($rentals as $rental) {
            $product = $rental->product;
            if ($product) {
                $existing = $topRentedProducts->firstWhere('id', $product->id);
                if ($existing) {
                    $existing['count'] += 1;
                    $existing['total'] += $rental->price;
                } else {
                    $topRentedProducts->push([
                        'id' => $product->id,
                        'nama_produk' => $product->nama_produk,
                        'count' => 1,
                        'total' => $rental->price,
                    ]);
                }
            }
        }
        $topRentedProducts = $topRentedProducts->sortByDesc('count')->take(5);

        return view('SellerView.reports.rentals', compact(
            'rentals',
            'totalRentalIncome',
            'totalRentals',
            'topRentedProducts',
            'startDate',
            'endDate'
        ));
    }
}