@php
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

$userId = Auth::id();

/* =========================
   📦 DATA DASAR
========================= */
$products = \App\Models\SellerModels\Product_seller::where('user_id', $userId)->get();
$productIds = $products->pluck('id');

$orders = \App\Models\SellerModels\Order_seller::with(['details.product'])
    ->whereHas('details', fn ($q) => $q->whereIn('product_id', $productIds))
    ->get();

$ordersDone = $orders->where('status','selesai');

$pendingOrders = $orders->whereIn('status',['menunggu','diproses'])->count();
$totalRevenue = $ordersDone->sum('total');

/* =========================
   📊 SALES 7 HARI
========================= */
$labels = [];
$dataSales = [];

for ($i = 6; $i >= 0; $i--) {
    $date = Carbon::now()->subDays($i)->format('Y-m-d');

    $labels[] = Carbon::now()->subDays($i)->format('d M');

    $total = $ordersDone->filter(function ($o) use ($date) {
        return Carbon::parse($o->created_at)->format('Y-m-d') == $date;
    })->sum('total');

    $dataSales[] = $total;
}

/* =========================
   ⭐ PRODUCT QUALITY
========================= */
$productRatings = \App\Models\SellerModels\ProductRating_seller::whereIn('product_id', $productIds)->get();
$avgProductRating = $productRatings->avg('rating') ?? 0;
$qualityScore = round(($avgProductRating / 5) * 100);

/* =========================
   💬 CHAT SPEED (sementara)
========================= */
$chatScore = 85;

/* =========================
   📦 STOCK DATA
========================= */
$totalStock = $products->sum('stok');

$totalOrderedQty = $ordersDone->sum(function ($order) {
    return $order->details->sum('qty');
});

$stockScore = $totalStock > 0
    ? max(50, min(100, 100 - (($totalOrderedQty / $totalStock) * 100)))
    : 100;

/* =========================
   📈 TREND
========================= */
$trendUp = collect($dataSales)->last() > collect($dataSales)->first();

/* =========================
   🎒 RENTED GEAR
========================= */
$rentedGear = $rental
    ->whereIn('status', ['Menunggu','Dikonfirmasi','Aktif'])
    ->sum(function ($rental) {
        return $rental->details
            ->filter(function ($d) {
                return optional($d->product)->kategori === 'sewa';
            })
            ->sum('qty');
    });

/* =========================
   🔍 respon massage 
========================= */
$messages = \App\Models\Chat::where('receiver_id', $userId)->get();

$responseTimes = [];

foreach ($messages as $msg) {
    $reply = \App\Models\Chat::where('sender_id', $userId)
        ->where('created_at', '>', $msg->created_at)
        ->first();

    if ($reply) {
        $diff = $reply->created_at->diffInMinutes($msg->created_at);
        $responseTimes[] = $diff;
    }
}

$avgResponse = count($responseTimes) 
    ? array_sum($responseTimes)/count($responseTimes) 
    : 0;

/* convert ke score */
$chatScore = $avgResponse == 0 ? 100 : max(40, 100 - $avgResponse);

/* =========================
   📥 RENTAL REQUEST
========================= */
$rentalRequests = $orders
    ->where('status', 'menunggu')
    ->filter(function ($order) {
        return $order->details->contains(function ($d) {
            return optional($d->product)->jenis_produk === 'sewa';
        });
    });

$totalRentalRequests = $rentalRequests->count();
@endphp


@extends('SellerView.layouts.app_seller')

@section('content')
<div class="d-flex" style="min-height:100vh; background:#f9fafb;">

   {{-- SIDEBAR --}}
    <div style="width:260px; background:#ffffff; border-right:1px solid #e5e7eb; display:flex; flex-direction:column; justify-content:space-between;">

        {{-- TOP --}}
        <div>

            {{-- BRAND --}}
            <div class="p-4 border-bottom">
                <h4 style="color:#10B981; font-weight:800; letter-spacing:1px;">CAMPIFY.</h4>
                <small class="text-muted">SELLER HUB</small>
            </div>

            {{-- MENU --}}
            <ul class="nav flex-column px-3 mt-3">

                {{-- DASHBOARD --}}
                <li class="nav-item mb-1">
                    <a class="nav-link sidebar-link {{ request()->routeIs('seller.dashboard') ? 'active' : '' }}"
                    href="{{ route('seller.dashboard') }}">
                        📊 Dashboard
                    </a>
                </li>

                {{-- PRODUK --}}
                <li class="nav-item mb-1">
                    <a class="nav-link sidebar-link {{ request()->routeIs('products*') ? 'active' : '' }}"
                    href="{{ route('seller.products.index') }}">
                        📦 Kelola Produk
                    </a>
                </li>

                {{-- RATING --}}
                <li class="nav-item mb-1">
                    <a class="nav-link sidebar-link {{ request()->routeIs('seller.ratings.index') ? 'active' : '' }}"
                    href="/seller/ratings">
                        ⭐ Kelola Rating
                    </a>
                </li>

                {{-- TRANSAKSI (DROPDOWN) --}}
                <li class="nav-item mb-1">

                    <a class="nav-link sidebar-link d-flex justify-content-between align-items-center"
                    data-bs-toggle="collapse"
                    href="#transaksiMenu"
                    role="button"
                    aria-expanded="false"
                    aria-controls="transaksiMenu">

                        💰 Transaksi
                        <span class="text-muted">▾</span>

                    </a>

                    <div class="collapse {{ request()->is('seller/orders*') || request()->is('seller/rentals*') ? 'show' : '' }}"
                        id="transaksiMenu">

                        <ul class="nav flex-column ms-3 mt-1">

                            <li class="nav-item">
                                <a class="nav-link sidebar-sub {{ request()->is('seller/orders*') ? 'active' : '' }}"
                                href="/seller/orders">
                                    🧾 Pesanan Baru
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link sidebar-sub {{ request()->is('seller/rentals*') ? 'active' : '' }}"
                                href="/seller/rentals">
                                    🏕️ Penyewaan Alat
                                </a>
                            </li>

                        </ul>

                    </div>
                </li>

                {{-- CHAT --}}
                <li class="nav-item mb-1">
                    <a class="nav-link sidebar-link {{ request()->routeIs('chat.index') ? 'active' : '' }}"
                    href="/seller/chat">
                        💬 Chat Pembeli
                    </a>
                </li>

            </ul>
        </div>

        {{-- BOTTOM --}}
        <div class="px-3 pb-4">
            <hr>
            <a class="nav-link sidebar-link {{ request()->routeIs('seller.store-profile*') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}" href="{{ route('seller.store-profile.index') }}"">
                👤 Profil Toko
            </a>
        </div>
    </div>

    {{-- MAIN --}}
    <div class="flex-grow-1 p-4">

        <h4 class="fw-bold">DASHBOARD</h4>
        <small class="text-muted">Pantau performa tokomu hari ini.</small>

        {{-- CARDS --}}
        <div class="row g-3 mt-3 mb-4">

            <div class="col-md-3">
                <div class="card p-3 shadow-sm border-0 rounded-4">
                    <small>Revenue</small>
                    <h5 class="fw-bold">Rp {{ number_format($totalRevenue,0,',','.') }}</h5>

                    @if($trendUp)
                        <small class="text-success">↑ Meningkat</small>
                    @else
                        <small class="text-danger">↓ Menurun</small>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-3 shadow-sm border-0 rounded-4">
                    <small>Orders</small>
                    <h5 class="fw-bold">{{ $orders->count() }}</h5>
                    <small class="text-danger">{{ $pendingOrders }} pending</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-3 shadow-sm border-0 rounded-4">
                    <small>Rented Gear</small>
                    <h5 class="fw-bold">{{ $rentedGear }}</h5>
                    <small class="text-primary">Sedang disewa</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-3 shadow-sm border-0 rounded-4">
                    <small>Rating</small>
                    <h5 class="fw-bold">{{ number_format($avgProductRating,1) }}/5</h5>
                </div>
            </div>

            <div class="col-md-3">
                <a href="{{ route('seller.rentals.index') }}" class="text-decoration-none text-dark">
                    <div class="card p-3 shadow-sm border-0 rounded-4">
                        <small>Permintaan Sewa</small>
                        <h5 class="fw-bold">{{ $totalRentalRequests }}</h5>

                        @if($totalRentalRequests > 0)
                            <small class="text-danger">
                                {{ $totalRentalRequests }} perlu diproses
                            </small>
                        @else
                            <small class="text-success">Semua aman</small>
                        @endif
                    </div>
                </a>
            </div>
        </div>

        <div class="row g-4">

            {{-- LEFT --}}
            <div class="col-md-8">

                {{-- SALES CHART --}}
                <div class="card p-3 shadow-sm border-0 rounded-4 mb-4">
                    <h6 class="fw-bold">Sales Overview</h6>
                    <canvas id="salesChart" height="120"></canvas>
                </div>

                {{-- RECENT ORDERS --}}
                <div class="card p-3 shadow-sm border-0 rounded-4">
                    <div class="d-flex justify-content-between">
                        <h6 class="fw-bold">Recent Orders</h6>
                    </div>

                    <table class="table mt-3">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Produk</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                        @forelse($orders->take(5) as $o)
                        <tr>
                            <td>#{{ $o->id }}</td>

                            <td>
                                {{ optional($o->details->first())->product->nama_produk ?? '-' }}
                            </td>

                            <td>
                                <span class="badge bg-secondary">
                                    {{ $o->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">
                                Belum ada pesanan
                            </td>
                        </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

            {{-- RIGHT --}}
            <div class="col-md-4">

                {{-- PERFORMANCE --}}
                <div class="card p-3 shadow-sm border-0 rounded-4 mb-3">
                    <h6 class="fw-bold mb-3">Seller Performance</h6>

                    {{-- Kualitas Produk --}}
                    <div class="d-flex justify-content-between">
                        <small>Kualitas Produk</small>
                        <small class="fw-semibold">{{ number_format($qualityScore, 1) }}%</small>
                    </div>
                    <div class="progress mb-3" style="height:6px;">
                        <div class="progress-bar bg-success" 
                            style="width:{{ $qualityScore }}%">
                        </div>
                    </div>

                    {{-- Chat Speed --}}
                    <div class="d-flex justify-content-between">
                        <small>Chat Speed</small>
                        <small class="fw-semibold">{{ number_format($chatScore, 1) }}%</small>
                    </div>
                    <div class="progress mb-3" style="height:6px;">
                        <div class="progress-bar bg-info" 
                            style="width:{{ $chatScore }}%">
                        </div>
                    </div>

                    {{-- Stock Accuracy --}}
                    <div class="d-flex justify-content-between">
                        <small>Stock Accuracy</small>
                        <small class="fw-semibold">{{ number_format($stockScore, 1) }}%</small>
                    </div>
                    <div class="progress" style="height:6px;">
                        <div class="progress-bar bg-warning" 
                            style="width:{{ $stockScore }}%">
                        </div>
                    </div>
                </div>

                {{-- HELP --}}
                <div class="card p-3 text-white rounded-4 border-0"
                     style="background:linear-gradient(135deg,#10B981,#065F46);">
                    <h6 class="fw-bold">Butuh Bantuan?</h6>
                    <p class="small">Tim support siap bantu tokomu 24/7</p>
                    <button class="btn btn-light btn-sm">Hubungi Support</button>
                </div>

            </div>

        </div>

    </div>
</div>

{{-- CHART --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels = @json($labels);
const dataSales = @json($dataSales);

new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            data: dataSales,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        plugins: { legend: { display: false } }
    }
});
</script>

@endsection

