<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
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
        $hasCreatedAt = Schema::hasColumn('transactions', 'created_at');

        if ($hasCreatedAt) {
            $transactionStats = Transaction::selectRaw('MONTH(created_at) as month, COUNT(*) as count, SUM(total) as revenue')
                ->whereYear('created_at', now()->year)
                ->groupByRaw('MONTH(created_at)')
                ->get();

            foreach ($transactionStats as $stat) {
                $monthlyTransactionCounts[$stat->month] = (int) $stat->count;
                $monthlyRevenue[$stat->month] = (int) $stat->revenue;
            }
        }

        $latestTransactions = Transaction::with(['user', 'product'])
            ->when($hasCreatedAt, fn($query) => $query->latest('created_at'))
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'users' => User::count(),
            'regularUsers' => User::where('role', 'buyer')->count(),
            'products' => Product::count(),
            'pendingProducts' => Product::where('status', 'pending')->count(),
            'transactions' => Transaction::count(),
            'revenue' => Transaction::sum('total'),
            'stores' => Store::count(),
            'bannedStores' => Store::where('status', 'banned')->count(),
            'flaggedChats' => Chat::where('is_flagged', true)->count(),
            'monthlyTransactionCounts' => array_values($monthlyTransactionCounts),
            'monthlyRevenue' => array_values($monthlyRevenue),
            'latestTransactions' => $latestTransactions,
            'hasCreatedAt' => $hasCreatedAt,
        ]);
    }
}
