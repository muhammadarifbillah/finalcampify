<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Order;
use App\Models\Product;
use App\Models\Report;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Violation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class MonitoringController extends Controller
{
    public function index()
    {
        $orders = Order::with(['buyer', 'details.product'])->latest()->get();
        $transactions = Transaction::with(['user', 'product'])
            ->when(Schema::hasColumn('transactions', 'created_at'), fn ($query) => $query->latest())
            ->get();
        $products = Product::with(['store', 'owner', 'seller'])->latest()->limit(12)->get();
        $sellers = User::where('role', 'seller')->latest()->limit(10)->get();
        $buyers = User::where('role', 'buyer')->latest()->limit(10)->get();
        $stores = Store::with('user')->latest()->limit(10)->get();
        $reports = Report::with(['reporter', 'seller', 'product'])->latest()->get();
        $violations = Violation::with(['seller', 'admin', 'product'])->latest()->get();
        $statusSummary = $orders->groupBy('status')->map->count();
        $activityLabels = [];
        $orderActivity = [];
        $productActivity = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $activityLabels[] = $date->format('d M');
            $orderActivity[] = $orders->filter(fn ($order) => $order->created_at?->isSameDay($date))->count();
            $productActivity[] = $products->filter(fn ($product) => $product->created_at?->isSameDay($date))->count();
        }

        return view('admin.monitoring', [
            'orders' => $orders,
            'transactions' => $transactions,
            'reports' => $reports,
            'violations' => $violations,
            'products' => $products,
            'sellers' => $sellers,
            'buyers' => $buyers,
            'stores' => $stores,
            'flaggedChats' => Chat::where('is_flagged', true)->count(),
            'statusSummary' => $statusSummary,
            'activityLabels' => $activityLabels,
            'orderActivity' => $orderActivity,
            'productActivity' => $productActivity,
        ]);
    }

    public function action(Request $request, User $seller)
    {
        abort_unless($seller->role === 'seller', 422);

        $data = $request->validate([
            'action' => 'required|in:warning,suspend,ban',
            'reason' => 'required|string|max:1000',
            'report_id' => 'nullable|exists:reports,id',
            'product_id' => 'nullable|exists:products,id',
        ]);

        $strikeCount = Violation::where('seller_id', $seller->id)->count() + 1;
        $action = $data['action'];

        if ($strikeCount >= 3 && $action === 'warning') {
            $action = 'suspend';
        }
        if ($strikeCount >= 5) {
            $action = 'ban';
        }

        Violation::create([
            'seller_id' => $seller->id,
            'admin_id' => \Illuminate\Support\Facades\Auth::id(),
            'report_id' => $data['report_id'] ?? null,
            'product_id' => $data['product_id'] ?? null,
            'source' => ($data['report_id'] ?? null) ? 'report' : 'admin',
            'action' => $action,
            'strike_count' => $strikeCount,
            'reason' => $data['reason'],
        ]);

        if (!empty($data['report_id'])) {
            Report::whereKey($data['report_id'])->update(['status' => 'reviewed']);
        }

        if ($action === 'suspend') {
            $seller->update(['status' => 'suspended']);
            $seller->store?->update(['status' => 'suspended', 'catatan_admin' => $data['reason']]);
        }

        if ($action === 'ban') {
            $seller->update(['status' => 'banned']);
            $seller->store?->update(['status' => 'banned', 'catatan_admin' => $data['reason']]);
        }

        return back()->with('success', 'Aksi seller tersimpan.');
    }
}
