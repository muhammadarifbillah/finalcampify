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
            ->sum(fn($r) => ($r->price * $r->duration) * 0.9);

        $totalAdminFees = 0;

        return view('SellerView.reports.index_seller', compact('totalSales', 'totalRentals', 'totalAdminFees'));
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
        $topProductsData = [];
        foreach ($orders as $order) {
            foreach ($order->details as $detail) {
                $product = $detail->product;
                if ($product) {
                    $pid = $product->id;
                    if (isset($topProductsData[$pid])) {
                        $topProductsData[$pid]['quantity'] += $detail->qty;
                        $topProductsData[$pid]['total'] += $detail->harga * $detail->qty;
                    } else {
                        $topProductsData[$pid] = [
                            'id' => $product->id,
                            'nama_produk' => $product->nama_produk,
                            'quantity' => $detail->qty,
                            'total' => $detail->harga * $detail->qty,
                        ];
                    }
                }
            }
        }
        $topProducts = collect($topProductsData)->sortByDesc('quantity')->take(5);

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
    |
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

        // Hitung total pendapatan sewa & jumlah transaksi langsung dari rentals yang ditampilkan
        $totalRentalIncome = $rentals->sum(fn($r) => ($r->price * $r->duration) * 0.9);
        $totalAdminFees = $rentals->sum(fn($r) => ($r->price * $r->duration) * 0.1);
        $totalRentals = $rentals->count();

        // Produk tersewa terbanyak
        $topRentedProductsData = [];
        foreach ($rentals as $rental) {
            $product = $rental->product;
            if ($product) {
                $pid = $product->id;
                if (isset($topRentedProductsData[$pid])) {
                    $topRentedProductsData[$pid]['count'] += 1;
                    $topRentedProductsData[$pid]['total'] += ($rental->price * $rental->duration);
                } else {
                    $topRentedProductsData[$pid] = [
                        'id' => $product->id,
                        'nama_produk' => $product->nama_produk,
                        'count' => 1,
                        'total' => ($rental->price * $rental->duration),
                    ];
                }
            }
        }
        $topRentedProducts = collect($topRentedProductsData)->sortByDesc('count')->take(5);

        return view('SellerView.reports.rentals', compact(
            'rentals',
            'totalRentalIncome',
            'totalAdminFees',
            'totalRentals',
            'topRentedProducts',
            'startDate',
            'endDate'
        ));
    }
}