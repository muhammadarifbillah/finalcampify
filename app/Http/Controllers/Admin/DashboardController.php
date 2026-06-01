<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\User;
use App\Services\OrderDisbursementService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function exportReport(Request $request, OrderDisbursementService $disbursements)
    {
        $type = $request->query('type', 'summary');
        $from = $request->query('from');
        $to = $request->query('to');
        $dateLabel = '';

        if ($from && $to) {
            $dateLabel = '_' . $from . '_sd_' . $to;
        }

        $printDate = now()->format('d/m/Y H:i:s');
        $formattedFrom = $from ? Carbon::parse($from)->format('d/m/Y') : null;
        $formattedTo = $to ? Carbon::parse($to)->format('d/m/Y') : null;

        $pdfData = [
            'printDate' => $printDate,
            'from' => $formattedFrom,
            'to' => $formattedTo,
        ];

        switch ($type) {
            case 'summary':
                $pdfData['userTotal'] = User::count();
                $pdfData['buyers'] = User::where('role', 'buyer')->count();
                $pdfData['sellers'] = User::where('role', 'seller')->count();
                $pdfData['pendingKyc'] = User::whereNotNull('ktp_image')->whereNull('ktp_verified_at')->count();

                $pdfData['productTotal'] = Product::count();
                $pdfData['productApproved'] = Product::where('status', 'approved')->count();
                $pdfData['productWaiting'] = Product::whereIn('status', ['waiting', 'pending'])->count();
                $pdfData['productRejected'] = Product::where('status', 'rejected')->count();
                $pdfData['newProductsThisWeek'] = Product::where('created_at', '>=', now()->startOfWeek())->count();

                $pdfData['orderCount'] = Order::count();
                $pdfData['orderRevenue'] = Order::sum('total');
                $pdfData['rentalCount'] = Order::whereHas('details', fn($q) => $q->where('type', 'rent'))->count();
                $pdfData['buyCount'] = Order::whereHas('details', fn($q) => $q->where('type', 'buy'))->count();

                $pdfData['totalEscrow'] = \App\Models\ReturnEscrow::whereIn('status', ['pending', 'checking'])->sum(DB::raw('deposit_amount + COALESCE(to_buyer, 0)'));
                $pdfData['jaminanSewa'] = \App\Models\ReturnEscrow::where('type', 'sewa')->whereIn('status', ['pending', 'checking'])->sum('deposit_amount');
                $pdfData['checkingReturns'] = \App\Models\ReturnEscrow::where('status', 'checking')->count();
                $pdfData['pendingReturns'] = \App\Models\ReturnEscrow::where('status', 'pending')->count();
                $pdfData['overdueReturns'] = \App\Models\ReturnEscrow::where('type', 'sewa')->whereNull('actual_date')->where('expected_date', '<', now())->count();
                $pdfData['totalLateFees'] = \App\Models\ReturnEscrow::sum('late_fee');
                $pdfData['adminRevenue'] = \App\Models\ReturnEscrow::where('status', 'completed')->sum(DB::raw('rental_fee_amount * 0.1'));

                $pdfData['storeTotal'] = Store::count();
                $pdfData['storeActive'] = Store::where('status', 'active')->count();
                $pdfData['storeBanned'] = Store::where('status', 'banned')->count();

                $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                $monthlyOrders = Order::selectRaw('MONTH(created_at) as month, COUNT(*) as count, SUM(total) as revenue')
                    ->whereYear('created_at', now()->year)
                    ->groupByRaw('MONTH(created_at)')
                    ->pluck('revenue', 'month')->toArray();

                $monthlyOrderCounts = Order::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                    ->whereYear('created_at', now()->year)
                    ->groupByRaw('MONTH(created_at)')
                    ->pluck('count', 'month')->toArray();

                $monthlyUsers = User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                    ->whereYear('created_at', now()->year)
                    ->groupByRaw('MONTH(created_at)')
                    ->pluck('count', 'month')->toArray();

                $monthlyData = [];
                for ($i = 1; $i <= 12; $i++) {
                    $monthlyData[] = [
                        'bulan' => $months[$i - 1],
                        'transaksi' => $monthlyOrderCounts[$i] ?? 0,
                        'revenue' => $monthlyOrders[$i] ?? 0,
                        'pengguna_baru' => $monthlyUsers[$i] ?? 0,
                    ];
                }
                $pdfData['monthlyData'] = $monthlyData;

                $view = 'admin.reports.summary';
                break;

            case 'orders':
                $query = Order::with(['buyer', 'details.product.store']);
                if ($from && $to) {
                    $query->whereBetween('created_at', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()]);
                }
                if ($request->query('status')) {
                    $query->where('status', $request->query('status'));
                }
                if ($request->query('order_type')) {
                    $ot = $request->query('order_type');
                    if ($ot === 'buy') {
                        $query->whereHas('details', fn($d) => $d->where('type', 'buy'))
                            ->whereDoesntHave('details', fn($d) => $d->where('type', 'rent'));
                    } elseif ($ot === 'rent') {
                        $query->whereHas('details', fn($d) => $d->where('type', 'rent'))
                            ->whereDoesntHave('details', fn($d) => $d->where('type', 'buy'));
                    }
                }
                if ($request->query('search')) {
                    $s = $request->query('search');
                    $query->whereHas('buyer', fn($q) => $q->where('name', 'like', "%{$s}%"))->orWhere('id', $s);
                }
                $pdfData['orders'] = $query->latest()->get();
                $view = 'admin.reports.orders';
                break;

            case 'purchases':
                $q = Order::with(['buyer', 'details.product.store'])
                    ->whereHas('details', fn($d) => $d->where('type', 'buy'))
                    ->whereDoesntHave('details', fn($d) => $d->where('type', 'rent'));
                if ($from && $to) {
                    $q->whereBetween('created_at', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()]);
                }
                if ($request->query('status')) {
                    $q->where('status', $request->query('status'));
                }
                if ($request->query('search')) {
                    $s = $request->query('search');
                    $q->whereHas('buyer', fn($qq) => $qq->where('name', 'like', "%{$s}%"))->orWhere('id', $s);
                }
                $pdfData['orders'] = $q->latest()->get();
                $view = 'admin.reports.orders';
                break;

            case 'rentals':
                $q = Order::with(['buyer', 'details.product.store'])
                    ->whereHas('details', fn($d) => $d->where('type', 'rent'))
                    ->whereDoesntHave('details', fn($d) => $d->where('type', 'buy'));
                if ($from && $to) {
                    $q->whereBetween('created_at', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()]);
                }
                if ($request->query('status')) {
                    $q->where('status', $request->query('status'));
                }
                if ($request->query('search')) {
                    $s = $request->query('search');
                    $q->whereHas('buyer', fn($qq) => $qq->where('name', 'like', "%{$s}%"))->orWhere('id', $s);
                }
                $pdfData['orders'] = $q->latest()->get();
                $view = 'admin.reports.orders';
                break;

            case 'returns':
                $query = \App\Models\ReturnEscrow::with(['order.buyer', 'order.details.product.store']);
                if ($from && $to) {
                    $query->whereBetween('created_at', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()]);
                }
                if ($request->query('return_type')) {
                    $query->where('type', $request->query('return_type'));
                }
                if ($request->query('status')) {
                    $query->where('status', $request->query('status'));
                }
                $pdfData['returns'] = $query->latest()->get();
                $view = 'admin.reports.returns';
                break;

            case 'return-buy':
                $qrb = \App\Models\ReturnEscrow::with(['order.buyer', 'order.details.product.store'])
                    ->where('type', 'jual_beli');
                if ($from && $to) {
                    $qrb->whereBetween('created_at', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()]);
                }
                if ($request->query('status')) {
                    $qrb->where('status', $request->query('status'));
                }
                $pdfData['returns'] = $qrb->latest()->get();
                $view = 'admin.reports.returns_buy';
                break;

            case 'return-rent':
                $qrr = \App\Models\ReturnEscrow::with(['order.buyer', 'order.details.product.store'])
                    ->where('type', 'sewa');
                if ($from && $to) {
                    $qrr->whereBetween('created_at', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()]);
                }
                if ($request->query('status')) {
                    $qrr->where('status', $request->query('status'));
                }
                $pdfData['returns'] = $qrr->latest()->get();
                $pdfData['returnType'] = 'sewa';
                $pdfData['returnTypeLabel'] = 'Pengembalian Sewa';
                $view = 'admin.reports.returns';
                break;

            case 'disbursements':
                $qso = \App\Models\SellerOrder::with(['order.buyer', 'store.user', 'seller', 'items.product', 'payout']);
                $disbursements->applyStatusFilter($qso, $request->query('status', 'pending'));
                $pdfData['sellerOrders'] = $qso->latest()->get();
                $view = 'admin.reports.disbursements';
                break;

            case 'users':
                $pdfData['users'] = User::withCount('orders')->get();
                $view = 'admin.reports.users';
                break;

            case 'products':
                $pdfData['products'] = Product::with(['store', 'category'])->get();
                $view = 'admin.reports.products';
                break;

            default:
                return redirect()->back()->with('error', 'Tipe laporan tidak valid.');
        }

        $filename = 'laporan_' . $type . $dateLabel . '_' . now()->format('Ymd_His') . '.pdf';

        $pdf = Pdf::loadView($view, $pdfData);

        // Use custom paper size and options if needed (A4, Portrait)
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }

    public function index()
    {
        $monthlyTransactionCounts = array_fill(1, 12, 0);
        $monthlyRevenue = array_fill(1, 12, 0);
        $monthlyUserActivity = array_fill(1, 12, 0);
        $hasTransactionCreatedAt = Schema::hasColumn('transactions', 'created_at');
        $hasOrderCreatedAt = Schema::hasTable('orders') && Schema::hasColumn('orders', 'created_at');

        if ($hasOrderCreatedAt) {
            $transactionStats = Order::selectRaw('MONTH(created_at) as month, COUNT(*) as count, SUM(total) as revenue')
                ->whereYear('created_at', now()->year)
                ->groupByRaw('MONTH(created_at)')
                ->get();

            foreach ($transactionStats as $stat) {
                $monthlyTransactionCounts[$stat->month] = (int) $stat->count;
                $monthlyRevenue[$stat->month] = (int) $stat->revenue;
            }
        } elseif ($hasTransactionCreatedAt) {
            $transactionStats = Transaction::selectRaw('MONTH(created_at) as month, COUNT(*) as count, SUM(total) as revenue')
                ->whereYear('created_at', now()->year)
                ->groupByRaw('MONTH(created_at)')
                ->get();

            foreach ($transactionStats as $stat) {
                $monthlyTransactionCounts[$stat->month] = (int) $stat->count;
                $monthlyRevenue[$stat->month] = (int) $stat->revenue;
            }
        }

        if (Schema::hasColumn('users', 'created_at')) {
            $userStats = User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', now()->year)
                ->groupByRaw('MONTH(created_at)')
                ->get();

            foreach ($userStats as $stat) {
                $monthlyUserActivity[$stat->month] = (int) $stat->count;
            }
        }

        $latestOrders = Order::with(['buyer', 'details.product'])
            ->when($hasOrderCreatedAt, fn($query) => $query->latest('created_at'))
            ->limit(5)
            ->get();

        $latestTransactions = Transaction::with(['user', 'product'])
            ->when($hasTransactionCreatedAt, fn($query) => $query->latest('created_at'))
            ->limit(5)
            ->get();

        $orderCount = Order::count();
        $orderRevenue = Order::sum('total');

        $rentalCount = Order::whereHas('details', fn($q) => $q->where('type', 'rent'))->count();
        $buyCount = Order::whereHas('details', fn($q) => $q->where('type', 'buy'))->count();

        $legacyTransactionCount = Transaction::count();
        $legacyRevenue = Transaction::sum('total');
        $marketplaceTransactions = $orderCount ?: $legacyTransactionCount;
        $marketplaceRevenue = $orderRevenue ?: $legacyRevenue;
        $waitingStatuses = ['waiting', 'pending'];

        // Return Statistics
        $checkingReturns = \App\Models\ReturnEscrow::where('status', 'checking')->count();
        $pendingReturns = \App\Models\ReturnEscrow::where('status', 'pending')->count();

        $jaminanSewaEscrow = \App\Models\ReturnEscrow::where('type', 'sewa')->whereIn('status', ['pending', 'checking'])->sum('deposit_amount');
        $danaReturEscrow = \App\Models\ReturnEscrow::whereIn('status', ['pending', 'checking'])->sum('to_buyer');
        $sellerEscrow = \App\Models\ReturnEscrow::whereIn('status', ['pending', 'checking'])->sum('to_seller');
        $totalEscrow = $jaminanSewaEscrow + $danaReturEscrow;

        // Resolution Time (Avg days)
        $avgResolutionTime = \App\Models\ReturnEscrow::where('status', 'completed')
            ->whereNotNull('actual_date')
            ->selectRaw('AVG(DATEDIFF(actual_date, created_at)) as avg_days')
            ->first()->avg_days ?? 0;

        // New Products this week
        $newProductsThisWeek = Product::where('created_at', '>=', now()->startOfWeek())->count();

        // Overdue & Today Due
        $overdueQuery = \App\Models\ReturnEscrow::where('type', 'sewa')
            ->whereNull('actual_date')
            ->where('expected_date', '<', now());
        $overdueReturns = $overdueQuery->count();

        $todayDueRentals = \App\Models\ReturnEscrow::where('type', 'sewa')
            ->whereNull('actual_date')
            ->whereDate('expected_date', now()->today())
            ->count();

        $totalLateFees = \App\Models\ReturnEscrow::sum('late_fee');
        $totalReturnDeposit = \App\Models\ReturnEscrow::sum('deposit_amount');
        $totalReturnFines = \App\Models\ReturnEscrow::sum(\Illuminate\Support\Facades\DB::raw('COALESCE(late_fee, 0) + COALESCE(damage_fee, 0)'));
        $totalReturnToSeller = \App\Models\ReturnEscrow::sum('to_seller');

        $adminRentalRevenue = \App\Models\ReturnEscrow::where('status', 'completed')
            ->sum(\Illuminate\Support\Facades\DB::raw('rental_fee_amount * 0.1'));

        // Combined Issues List (for the tabbed table)
        $filter = request('filter', 'all');
        $issuesQuery = \App\Models\ReturnEscrow::with(['order.buyer', 'order.details.product']);

        if ($filter === 'checking') {
            $issuesQuery->where('status', 'checking');
        } elseif ($filter === 'pending') {
            $issuesQuery->where('status', 'pending');
        } elseif ($filter === 'late') {
            $issuesQuery->where('type', 'sewa')
                ->where('expected_date', '<', today())
                ->whereIn('status', ['pending', 'checking']);
        } else {
            $issuesQuery->whereIn('status', ['pending', 'checking']);
        }

        $allIssues = $issuesQuery->latest()->limit(10)->get();

        // Activity Feed (Orders + Returns + Reports)
        $recentOrders = Order::with(['buyer', 'details'])->latest()->limit(5)->get()->map(function ($o) {
            $isRental = $o->details->where('type', 'rent')->isNotEmpty();
            $typeLabel = $isRental ? 'menyewa alat' : 'melakukan pembelian';
            return ['type' => 'order', 'title' => ($o->buyer->name ?? 'User') . ' ' . $typeLabel, 'meta' => '#' . $o->id, 'time' => $o->created_at];
        });
        $recentReturns = \App\Models\ReturnEscrow::with('order.buyer')->latest()->limit(5)->get()->map(function ($r) {
            return ['type' => 'return', 'title' => ($r->order->buyer->name ?? 'User') . ' mengajukan retur', 'meta' => '#RT-' . $r->id, 'time' => $r->created_at];
        });
        $recentReports = \App\Models\Report::with('reporter')->latest()->limit(5)->get()->map(function ($r) {
            $reason = strlen($r->reason) > 25 ? substr($r->reason, 0, 25) . '...' : $r->reason;
            return ['type' => 'report', 'title' => ($r->reporter->name ?? 'User') . ' melaporkan ' . $r->type, 'meta' => 'Alasan: ' . $reason, 'time' => $r->created_at];
        });

        $activityFeed = $recentOrders->concat($recentReturns)->concat($recentReports)->sortByDesc('time')->take(8);

        return view('admin.dashboard', [
            'users' => User::count(),
            'buyers' => User::where('role', 'buyer')->count(),
            'sellers' => User::where('role', 'seller')->count(),
            'products' => Product::count(),
            'newProductsThisWeek' => $newProductsThisWeek,
            'pendingProducts' => Product::whereIn('status', $waitingStatuses)->count(),
            'approvedProducts' => Product::where('status', 'approved')->count(),
            'rejectedProducts' => Product::where('status', 'rejected')->count(),
            'transactions' => $marketplaceTransactions,
            'orders' => $orderCount,
            'rentalCount' => $rentalCount,
            'buyCount' => $buyCount,
            'revenue' => $marketplaceRevenue,
            'stores' => Store::count(),
            'activeStores' => Store::where('status', 'active')->count(),
            'bannedStores' => Store::where('status', 'banned')->count(),
            'flaggedChats' => Chat::where('is_flagged', true)->count(),
            'pendingKyc' => User::whereNotNull('ktp_image')->whereNull('ktp_verified_at')->count(),

            // Returns & Escrow
            'checkingReturns' => $checkingReturns,
            'pendingReturns' => $pendingReturns,
            'overdueReturns' => $overdueReturns,
            'todayDueRentals' => $todayDueRentals,
            'totalEscrow' => $totalEscrow,
            'jaminanSewaEscrow' => $jaminanSewaEscrow,
            'danaReturEscrow' => $danaReturEscrow,
            'totalReturnDeposit' => $totalReturnDeposit,
            'totalReturnFines' => $totalReturnFines,
            'totalReturnToSeller' => $totalReturnToSeller,
            'avgResolutionTime' => number_format($avgResolutionTime, 1),
            'totalLateFees' => $totalLateFees,
            'adminRentalRevenue' => $adminRentalRevenue,
            'allIssues' => $allIssues,
            'filter' => $filter,
            'activityFeed' => $activityFeed,

            'monthlyTransactionCounts' => array_values($monthlyTransactionCounts),
            'monthlyRevenue' => array_values($monthlyRevenue),
            'monthlyUserActivity' => array_values($monthlyUserActivity),
            'latestOrders' => $latestOrders,
            'latestTransactions' => $latestTransactions,
            'hasCreatedAt' => $hasOrderCreatedAt || $hasTransactionCreatedAt,
        ]);
    }
}
