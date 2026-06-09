<?php

namespace App\Http\Controllers\SellerController;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Models\ReturnEscrow;
use App\Models\SellerModels\Rental_seller;
use App\Models\SellerOrder;
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
        $totalSales = $this->sellerPurchaseOrdersQuery($sellerId)
            ->whereHas('payout', fn ($q) => $q->where('status', Payout::STATUS_DISBURSED))
            ->get()
            ->sum(fn ($order) => $this->sellerOrderPayoutAmount($order));

        $pendingSalesPayout = $this->sellerPurchaseOrdersQuery($sellerId)
            ->where('status', SellerOrder::STATUS_DELIVERED)
            ->whereDoesntHave('payout', fn ($q) => $q->where('status', Payout::STATUS_DISBURSED))
            ->get()
            ->sum(fn ($order) => $this->sellerOrderPayoutAmount($order));

        $rentalRows = $this->sellerRentalReportRows($sellerId);
        $totalRentals = $rentalRows->sum('report_net_income');
        $totalAdminFees = $rentalRows->sum('report_admin_fee');

        return view('SellerView.reports.index_seller', compact('totalSales', 'totalRentals', 'totalAdminFees', 'pendingSalesPayout'));
    }

    /**
     * Export Laporan ke PDF (Simulasi menggunakan view khusus print)
     */
    public function exportPdf(Request $request, $type)
    {
        $sellerId = Auth::id();
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        [$startAt, $endAt] = $this->dateBounds($startDate, $endDate);

        if ($type === 'sales') {
            $data = $this->sellerSalesReportOrders($sellerId, $startAt, $endAt);
            $title = "Laporan Penjualan Produk";
        } else {
            $data = $this->sellerRentalReportRows($sellerId, $startAt, $endAt);
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
        [$startAt, $endAt] = $this->dateBounds($startDate, $endDate);

        // Ambil seller order jual-beli yang delivered pada periode ini,
        // plus order lama yang payout-nya baru dicairkan pada periode ini.
        $orders = $this->sellerSalesReportOrders($sellerId, $startAt, $endAt);

        // Hitung total penjualan
        $totalSales = $orders->sum(fn ($order) => $this->sellerOrderPayoutAmount($order));
        $totalDisbursedSales = $orders
            ->filter(fn ($order) => $this->isPayoutDisbursedInPeriod($order, $startAt, $endAt))
            ->sum(fn ($order) => $this->sellerOrderPayoutAmount($order));
        $totalWaitingPayout = $orders
            ->reject(fn ($order) => $this->isPayoutDisbursedInPeriod($order, $startAt, $endAt))
            ->sum(fn ($order) => $this->sellerOrderPayoutAmount($order));
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
            'totalDisbursedSales',
            'totalWaitingPayout',
            'totalOrders',
            'topProducts',
            'startDate',
            'endDate'
        ));
    }

    public function payoutReport(Request $request)
    {
        $sellerId = Auth::id();
        $status = $request->get('status');
        $allowedStatuses = [
            Payout::STATUS_WAITING_DELIVERY,
            Payout::STATUS_WAITING_HOLD,
            Payout::STATUS_READY_TO_DISBURSE,
            Payout::STATUS_DISBURSED,
        ];

        $query = $this->sellerPurchaseOrdersQuery($sellerId)
            ->whereHas('payout');

        if (in_array($status, $allowedStatuses, true)) {
            $query->whereHas('payout', fn ($q) => $q->where('status', $status));
        }

        $payoutOrders = $query->latest()->paginate(15)->withQueryString();

        $summaryOrders = $this->sellerPurchaseOrdersQuery($sellerId)->whereHas('payout')->get();
        $totalDisbursed = $summaryOrders
            ->filter(fn ($order) => $order->payout?->status === Payout::STATUS_DISBURSED)
            ->sum(fn ($order) => (int) ($order->payout?->amount ?? $order->subtotal));
        $totalReady = $summaryOrders
            ->filter(fn ($order) => $order->payout?->status === Payout::STATUS_READY_TO_DISBURSE)
            ->sum(fn ($order) => (int) ($order->payout?->amount ?? $order->subtotal));
        $totalWaiting = $summaryOrders
            ->filter(fn ($order) => in_array($order->payout?->status, [
                Payout::STATUS_WAITING_DELIVERY,
                Payout::STATUS_WAITING_HOLD,
            ], true))
            ->sum(fn ($order) => (int) ($order->payout?->amount ?? $order->subtotal));

        return view('SellerView.reports.payouts', compact(
            'payoutOrders',
            'status',
            'totalDisbursed',
            'totalReady',
            'totalWaiting'
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
        [$startAt, $endAt] = $this->dateBounds($startDate, $endDate);

        // Ambil data dari menu penyewaan seller, lalu pakai settlement return jika sudah ada.
        $rentals = $this->sellerRentalReportRows($sellerId, $startAt, $endAt);

        // Hitung total pendapatan sewa & jumlah transaksi langsung dari rentals yang ditampilkan
        $totalRentalIncome = $rentals->sum('report_net_income');
        $totalAdminFees = $rentals->sum('report_admin_fee');
        $totalRentals = $rentals->count();

        // Produk tersewa terbanyak
        $topRentedProductsData = [];
        foreach ($rentals as $rental) {
            $product = $rental->product;
            if ($product) {
                $pid = $product->id;
                if (isset($topRentedProductsData[$pid])) {
                    $topRentedProductsData[$pid]['count'] += 1;
                    $topRentedProductsData[$pid]['total'] += (int) $rental->report_gross_amount;
                } else {
                    $topRentedProductsData[$pid] = [
                        'id' => $product->id,
                        'nama_produk' => $product->nama_produk,
                        'count' => 1,
                        'total' => (int) $rental->report_gross_amount,
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

    private function sellerPurchaseOrdersQuery(int $sellerId)
    {
        return SellerOrder::with(['items.product', 'order.buyer', 'store.user', 'seller', 'payout'])
            ->whereHas('items', fn ($query) => $query->where('type', 'buy'))
            ->whereDoesntHave('items', fn ($query) => $query->where('type', 'rent'))
            ->where(function ($query) use ($sellerId) {
                $query->where('seller_id', $sellerId)
                    ->orWhereHas('store', fn ($store) => $store->where('user_id', $sellerId));
            });
    }

    private function sellerSalesReportOrders(int $sellerId, Carbon $startAt, Carbon $endAt)
    {
        return $this->sellerPurchaseOrdersQuery($sellerId)
            ->where('status', SellerOrder::STATUS_DELIVERED)
            ->where(function ($query) use ($startAt, $endAt) {
                $query->whereBetween('created_at', [$startAt, $endAt])
                    ->orWhereHas('payout', function ($payout) use ($startAt, $endAt) {
                        $payout->where('status', Payout::STATUS_DISBURSED)
                            ->where(function ($dateQuery) use ($startAt, $endAt) {
                                $dateQuery->whereBetween('disbursed_at', [$startAt, $endAt])
                                    ->orWhere(function ($fallback) use ($startAt, $endAt) {
                                        $fallback->whereNull('disbursed_at')
                                            ->whereBetween('updated_at', [$startAt, $endAt]);
                                    });
                            });
                    });
            })
            ->latest()
            ->get()
            ->sortByDesc(fn ($order) => $this->salesReportDate($order)?->timestamp ?? 0)
            ->values();
    }

    private function sellerRentalReportRows(int $sellerId, ?Carbon $startAt = null, ?Carbon $endAt = null)
    {
        return $this->sellerRentalReportQuery($sellerId, $startAt, $endAt)
            ->get()
            ->map(fn ($rental) => $this->decorateRentalForReport($rental))
            ->sortByDesc(fn ($rental) => $rental->report_date?->timestamp ?? $rental->updated_at?->timestamp ?? 0)
            ->values();
    }

    private function sellerRentalReportQuery(int $sellerId, ?Carbon $startAt = null, ?Carbon $endAt = null)
    {
        return Rental_seller::with(['user', 'product.store', 'returnRequest'])
            ->whereHas('product', fn ($query) => $this->applySellerProductOwnership($query, $sellerId))
            ->where('status', '!=', 'cancelled')
            ->when($startAt && $endAt, function ($query) use ($startAt, $endAt) {
                $query->where(function ($dateQuery) use ($startAt, $endAt) {
                    $dateQuery->whereBetween('created_at', [$startAt, $endAt])
                        ->orWhere(function ($rentalPeriod) use ($startAt, $endAt) {
                            $this->applyRentalPeriodDateFilter($rentalPeriod, $startAt, $endAt);
                        })
                        ->orWhereHas('returnRequest', function ($returnQuery) use ($startAt, $endAt) {
                            $returnQuery->where('type', ReturnEscrow::TYPE_SEWA);
                            $this->applyReturnSettlementDateFilter($returnQuery, $startAt, $endAt);
                        });
                });
            });
    }

    private function applySellerProductOwnership($query, int $sellerId): void
    {
        $query->where(function ($productQuery) use ($sellerId) {
            $productQuery->where('user_id', $sellerId)
                ->orWhereHas('store', fn ($store) => $store->where('user_id', $sellerId));
        });
    }

    private function applyReturnSettlementDateFilter($query, Carbon $startAt, Carbon $endAt): void
    {
        $query->where(function ($dateQuery) use ($startAt, $endAt) {
            $dateQuery->whereBetween('refund_disbursed_at', [$startAt, $endAt])
                ->orWhere(function ($fallback) use ($startAt, $endAt) {
                    $fallback->whereNull('refund_disbursed_at')
                        ->whereBetween('updated_at', [$startAt, $endAt]);
                });
        });
    }

    private function applyRentalPeriodDateFilter($query, Carbon $startAt, Carbon $endAt): void
    {
        $startDate = $startAt->toDateString();
        $endDate = $endAt->toDateString();

        $query->where(function ($periodQuery) use ($startDate, $endDate) {
            $periodQuery->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function ($overlapQuery) use ($startDate, $endDate) {
                    $overlapQuery->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                });
        });
    }

    private function decorateRentalForReport(Rental_seller $rental): Rental_seller
    {
        $grossAmount = $this->rentalGrossAmount($rental);
        $netIncome = $this->rentalSellerIncome($rental, $grossAmount);
        $duration = max(1, (int) ($rental->duration ?? 1));

        $rental->setAttribute('report_gross_amount', $grossAmount);
        $rental->setAttribute('report_net_income', $netIncome);
        $rental->setAttribute('report_admin_fee', $this->rentalAdminFee($rental, $grossAmount));
        $rental->setAttribute('report_daily_price', (int) round($grossAmount / $duration));
        $rental->setAttribute('report_date', $this->rentalReportDate($rental));

        return $rental;
    }

    private function rentalGrossAmount(Rental_seller $rental): int
    {
        $settledRentalFee = (int) ($rental->returnRequest?->rental_fee_amount ?? 0);
        if ($settledRentalFee > 0) {
            return $settledRentalFee;
        }

        $legacyTotal = (int) ($rental->getAttribute('total_price') ?? $rental->getAttribute('total_harga') ?? 0);
        if ($legacyTotal > 0) {
            return $legacyTotal;
        }

        $price = (int) ($rental->price ?? 0);
        $duration = max(1, (int) ($rental->duration ?? 1));
        $productDailyPrice = (int) ($rental->product?->rent_price ?? 0);

        if ($productDailyPrice > 0 && $price <= $productDailyPrice) {
            return $price * $duration;
        }

        return $price;
    }

    private function rentalSellerIncome(Rental_seller $rental, int $grossAmount): int
    {
        $settledIncome = (int) ($rental->returnRequest?->to_seller ?? 0);
        if ($settledIncome > 0) {
            return $settledIncome;
        }

        return (int) round($grossAmount * 0.9);
    }

    private function rentalAdminFee(Rental_seller $rental, int $grossAmount): int
    {
        $settledRentalFee = (int) ($rental->returnRequest?->rental_fee_amount ?? 0);
        $baseAmount = $settledRentalFee > 0 ? $settledRentalFee : $grossAmount;

        return (int) round($baseAmount * 0.1);
    }

    private function rentalReportDate(Rental_seller $rental): ?Carbon
    {
        return $rental->returnRequest?->refund_disbursed_at
            ?? $rental->returnRequest?->actual_date
            ?? $rental->start_date
            ?? $rental->created_at;
    }

    private function sellerOrderPayoutAmount(SellerOrder $order): int
    {
        return (int) ($order->payout?->amount ?? $order->subtotal);
    }

    private function isPayoutDisbursedInPeriod(SellerOrder $order, Carbon $startAt, Carbon $endAt): bool
    {
        if ($order->payout?->status !== Payout::STATUS_DISBURSED) {
            return false;
        }

        $date = $this->payoutReportDate($order);

        return $date ? $date->between($startAt, $endAt, true) : false;
    }

    private function salesReportDate(SellerOrder $order): ?Carbon
    {
        if ($order->payout?->status === Payout::STATUS_DISBURSED) {
            return $this->payoutReportDate($order) ?? $order->created_at;
        }

        return $order->created_at;
    }

    private function payoutReportDate(SellerOrder $order): ?Carbon
    {
        return $order->payout?->disbursed_at
            ?? $order->payout?->updated_at
            ?? $order->payout?->created_at;
    }

    private function dateBounds(string $startDate, string $endDate): array
    {
        return [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay(),
        ];
    }
}
