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
    /**
     * Halaman Utama Laporan (Card View)
     */
    public function index()
    {
        $sellerId = Auth::id();
        
        // Ringkasan Cepat
        $totalSales = Order_seller::where('status', 'selesai')
            ->whereHas('details.product', fn($q) => $q->where('user_id', $sellerId))
            ->whereDoesntHave('details', fn($q) => $q->where('type', 'rent'))
            ->sum('total');

        $totalRentals = Rental_seller::where('status', 'completed')
            ->whereHas('product', fn($q) => $q->where('user_id', $sellerId))
            ->get()
            ->sum(fn($r) => $r->price * $r->duration);

        return view('SellerView.reports.index_seller', compact('totalSales', 'totalRentals'));
    }

    /**
     * Export Laporan ke PDF (Simulasi menggunakan view khusus print)
     */
    public function exportPdf(Request $request, $type)
    {
        $sellerId = Auth::id();
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        if ($type === 'sales') {
            $data = Order_seller::where('status', 'selesai')
                ->whereHas('details.product', fn($q) => $q->where('user_id', $sellerId))
                ->whereDoesntHave('details', fn($q) => $q->where('type', 'rent'))
                ->with(['buyer', 'details.product'])
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->get();
            $title = "Laporan Penjualan Produk";
        } else {
            $data = Rental_seller::where('status', 'completed')
                ->whereHas('product', fn($q) => $q->where('user_id', $sellerId))
                ->with(['user', 'product'])
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->get();
            $title = "Laporan Penyewaan Alat";
        }

        return view('SellerView.reports.export_pdf', compact('data', 'type', 'title', 'startDate', 'endDate'));
    }
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

        // Ambil orders yang sudah selesai dan tipe itemnya 'sell'
        $orders = Order_seller::where('status', 'selesai')
            ->whereHas('details.product', function($query) use ($sellerId) {
                $query->where('user_id', $sellerId);
            })
            ->whereDoesntHave('details', function($query) {
                $query->where('type', 'rent');
            })
            ->with(['buyer', 'details.product'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->latest()
            ->get();

        // Hitung total penjualan
        $totalSales = $orders->sum('total');
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
        $rentals = Rental_seller::where('status', 'completed')
            ->whereHas('product', function($query) use ($sellerId) {
                $query->where('user_id', $sellerId);
            })
            ->with(['user', 'product'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->latest()
            ->get();

        // Hitung total pendapatan sewa
        $totalRentalIncome = $rentals->sum(fn($r) => $r->price * $r->duration);
        $totalRentals = $rentals->count();

        // Produk tersewa terbanyak
        $topRentedProducts = collect();
        foreach ($rentals as $rental) {
            $product = $rental->product;
            if ($product) {
                $existing = $topRentedProducts->firstWhere('id', $product->id);
                if ($existing) {
                    $existing['count'] += 1;
                    $existing['total'] += ($rental->price * $rental->duration);
                } else {
                    $topRentedProducts->push([
                        'id' => $product->id,
                        'nama_produk' => $product->nama_produk,
                        'count' => 1,
                        'total' => ($rental->price * $rental->duration),
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