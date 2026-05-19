<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
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
        $recentOrders = Order::with(['buyer', 'details'])->latest()->limit(5)->get()->map(function($o) {
            $isRental = $o->details->where('type', 'rent')->isNotEmpty();
            $typeLabel = $isRental ? 'menyewa alat' : 'melakukan pembelian';
            return ['type' => 'order', 'title' => ($o->buyer->name ?? 'User') . ' ' . $typeLabel, 'meta' => '#' . $o->id, 'time' => $o->created_at];
        });
        $recentReturns = \App\Models\ReturnEscrow::with('order.buyer')->latest()->limit(5)->get()->map(function($r) {
            return ['type' => 'return', 'title' => ($r->order->buyer->name ?? 'User') . ' mengajukan retur', 'meta' => '#RT-' . $r->id, 'time' => $r->created_at];
        });
        $recentReports = \App\Models\Report::with('reporter')->latest()->limit(5)->get()->map(function($r) {
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
