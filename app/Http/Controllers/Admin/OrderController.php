<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payout;
use App\Models\SellerOrder;
use App\Services\OrderDisbursementService;
use App\Services\SellerOrderService;
use Illuminate\Http\Request;
use RuntimeException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['buyer', 'details.product.store'])
            ->latest('created_at');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type (buy/rent)
        if ($request->filled('type')) {
            if ($request->type === 'buy') {
                $query->whereHas('details', fn($q) => $q->where('type', 'buy'))
                    ->whereDoesntHave('details', fn($q) => $q->where('type', 'rent'));
            } elseif ($request->type === 'rent') {
                $query->whereHas('details', fn($q) => $q->where('type', 'rent'))
                    ->whereDoesntHave('details', fn($q) => $q->where('type', 'buy'));
            }
        }

        // Filter by search (buyer name or order id)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhereHas('buyer', fn($q2) => $q2->where('name', 'like', "%$search%"));
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        // Stats untuk Penyewaan
        $totalRentOrders = Order::whereHas('details', fn($q) => $q->where('type', 'rent'))
            ->whereDoesntHave('details', fn($q) => $q->where('type', 'buy'))
            ->count();
        $totalRentRevenue = Order::whereHas('details', fn($q) => $q->where('type', 'rent'))
            ->whereDoesntHave('details', fn($q) => $q->where('type', 'buy'))
            ->sum('total');
        $pendingRentOrders = Order::whereHas('details', fn($q) => $q->where('type', 'rent'))
            ->whereDoesntHave('details', fn($q) => $q->where('type', 'buy'))
            ->where('status', 'menunggu')
            ->count();
        $selesaiRentOrders = Order::whereHas('details', fn($q) => $q->where('type', 'rent'))
            ->whereDoesntHave('details', fn($q) => $q->where('type', 'buy'))
            ->where('status', 'selesai')
            ->count();

        // Stats untuk Pembelian
        $totalBuyOrders = Order::whereHas('details', fn($q) => $q->where('type', 'buy'))
            ->whereDoesntHave('details', fn($q) => $q->where('type', 'rent'))
            ->count();
        $totalBuyRevenue = Order::whereHas('details', fn($q) => $q->where('type', 'buy'))
            ->whereDoesntHave('details', fn($q) => $q->where('type', 'rent'))
            ->sum('total');
        $pendingBuyOrders = Order::whereHas('details', fn($q) => $q->where('type', 'buy'))
            ->whereDoesntHave('details', fn($q) => $q->where('type', 'rent'))
            ->where('status', 'menunggu')
            ->count();
        $selesaiBuyOrders = Order::whereHas('details', fn($q) => $q->where('type', 'buy'))
            ->whereDoesntHave('details', fn($q) => $q->where('type', 'rent'))
            ->where('status', 'selesai')
            ->count();

        $currentType = $request->type;

        return view('admin.orders', compact(
            'orders',
            'totalRentOrders',
            'totalRentRevenue',
            'pendingRentOrders',
            'selesaiRentOrders',
            'totalBuyOrders',
            'totalBuyRevenue',
            'pendingBuyOrders',
            'selesaiBuyOrders',
            'currentType'
        ));
    }

    public function disbursements(Request $request, OrderDisbursementService $disbursements)
    {
        $status = $request->input('status', 'pending');
        if (!in_array($status, ['pending', 'WAITING_DELIVERY', 'WAITING_HOLD', 'READY_TO_DISBURSE', 'DISBURSED', 'DITERIMA', 'DELIVERED'], true)) {
            $status = 'pending';
        }

        $query = SellerOrder::with(['order.buyer', 'store.user', 'seller', 'items.product', 'payout'])
            ->latest('updated_at');

        $disbursements->applyStatusFilter($query, $status);

        $sellerOrders = $query->paginate(15)->withQueryString();
        $eligibilityBySellerOrderId = $sellerOrders->getCollection()
            ->mapWithKeys(fn($sellerOrder) => [$sellerOrder->id => $disbursements->eligibility($sellerOrder)])
            ->all();

        $totalTertahanQuery = SellerOrder::query();
        $disbursements->applyPurchaseSellerOrderFilter($totalTertahanQuery);
        $disbursements->applyNotDisbursedFilter($totalTertahanQuery);

        $totalDicairkanQuery = Payout::query()
            ->where('status', Payout::STATUS_DISBURSED)
            ->whereHas('sellerOrder', fn($q) => $disbursements->applyPurchaseSellerOrderFilter($q));

        $totalSiapCairQuery = SellerOrder::query();
        $disbursements->applyReadyToDisburseFilter($totalSiapCairQuery);

        $deliveredQuery = SellerOrder::query();
        $disbursements->applyDeliveredFilter($deliveredQuery);

        $totalTertahan = $totalTertahanQuery->sum('subtotal');
        $totalDicairkan = $totalDicairkanQuery->sum('amount');
        $totalSiapCair = (clone $totalSiapCairQuery)->sum('subtotal');
        $readyCount = (clone $totalSiapCairQuery)->count();
        $deliveredCount = (clone $deliveredQuery)->count();

        return view('admin.disbursements', compact('sellerOrders', 'eligibilityBySellerOrderId', 'totalTertahan', 'totalDicairkan', 'totalSiapCair', 'readyCount', 'deliveredCount', 'status'));
    }

    public function disburse(SellerOrder $sellerOrder, Request $request, OrderDisbursementService $disbursements)
    {
        try {
            $disbursements->disburse($sellerOrder, $request->user(), 'manual');
        } catch (RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Dana seller order ' . ($sellerOrder->seller_order_number ?? '#' . $sellerOrder->id) . ' berhasil ditandai telah dicairkan ke penjual.');
    }

    public function resyncSellerOrders(SellerOrderService $sellerOrderService)
    {
        $orders = Order::with(['details.product.store'])->get();

        foreach ($orders as $order) {
            $sellerOrderService->syncForOrder($order);
        }

        return back()->with('success', 'Semua seller order berhasil di-sync ulang dari data order terbaru.');
    }

    public function showDisbursement(SellerOrder $sellerOrder, OrderDisbursementService $disbursements)
    {
        $sellerOrder->load(['order.buyer', 'store.user', 'seller', 'items.product', 'payout']);

        $eligibility = $disbursements->eligibility($sellerOrder);
        $readyToDisburse = $eligibility['ready'];
        $readyAt = $eligibility['ready_at'];
        $daysUntilReady = $eligibility['days_until_ready'];
        $store = $sellerOrder->store;
        $bankName = $store?->bank_name ?? $store?->user?->bank_name ?? '-';
        $bankAccount = $store?->bank_account_number ?? $store?->user?->bank_account_number ?? '-';
        $bankOwner = $store?->bank_account_name ?? $store?->user?->bank_account_name ?? '-';

        return view('admin.disbursements.show', compact('sellerOrder', 'eligibility', 'readyToDisburse', 'readyAt', 'daysUntilReady', 'bankName', 'bankAccount', 'bankOwner'));
    }
}
