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
        $legacyTransactionCount = Transaction::count();
        $legacyRevenue = Transaction::sum('total');
        $marketplaceTransactions = $orderCount ?: $legacyTransactionCount;
        $marketplaceRevenue = $orderRevenue ?: $legacyRevenue;
        $waitingStatuses = ['waiting', 'pending'];

        return view('admin.dashboard', [
            'users' => User::count(),
            'buyers' => User::where('role', 'buyer')->count(),
            'sellers' => User::where('role', 'seller')->count(),
            'regularUsers' => User::where('role', 'buyer')->count(),
            'products' => Product::count(),
            'pendingProducts' => Product::whereIn('status', $waitingStatuses)->count(),
            'approvedProducts' => Product::where('status', 'approved')->count(),
            'rejectedProducts' => Product::where('status', 'rejected')->count(),
            'transactions' => $marketplaceTransactions,
            'orders' => $orderCount,
            'revenue' => $marketplaceRevenue,
            'stores' => Store::count(),
            'activeStores' => Store::where('status', 'active')->count(),
            'bannedStores' => Store::where('status', 'banned')->count(),
            'flaggedChats' => Chat::where('is_flagged', true)->count(),
            'pendingKyc' => User::whereNotNull('ktp_image')->whereNull('ktp_verified_at')->count(),
            'monthlyTransactionCounts' => array_values($monthlyTransactionCounts),
            'monthlyRevenue' => array_values($monthlyRevenue),
            'monthlyUserActivity' => array_values($monthlyUserActivity),
            'latestOrders' => $latestOrders,
            'latestTransactions' => $latestTransactions,
            'hasCreatedAt' => $hasOrderCreatedAt || $hasTransactionCreatedAt,
        ]);
    }
}
