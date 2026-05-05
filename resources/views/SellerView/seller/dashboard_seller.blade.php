@php
$userId = \Illuminate\Support\Facades\Auth::id();
$products = \App\Models\SellerModels\Product_seller::where('user_id', $userId)->get();
$productIds = $products->pluck('id');
$orders = \App\Models\SellerModels\Order_seller::with(['details.product'])
    ->whereHas('details', fn ($query) => $query->whereIn('product_id', $productIds))
    ->get();
$pendingOrders = $orders->whereIn('status', ['menunggu', 'diproses'])->count();
$totalRevenue = $orders
    ->where('status', 'selesai')
    ->sum('total');

// Rating toko
$avgStoreRating = \App\Models\SellerModels\StoreRating_seller::getAverageRating($userId);
$storeRatingCount = \App\Models\SellerModels\StoreRating_seller::getRatingCount($userId);
@endphp

@extends('SellerView.layouts.app_seller')

@section('content')
<div class="d-flex" style="min-height:100vh; background:#f9fafb;">

    <div style="width:260px; background:white; border-right:1px solid #eee; display:flex; flex-direction:column; justify-content:space-between;">

        {{-- TOP --}}
        <div>
            <div class="p-4">
                <h4 style="color:#10B981; font-weight:800;">CAMPIFY.</h4>
                <small class="text-muted">SELLER HUB</small>
            </div>

            <ul class="nav flex-column px-3">

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('seller.dashboard') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                    href="{{ route('seller.dashboard') }}">
                    Dashboard
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('seller.products.index') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                    href="{{ route('seller.products.index') }}">
                    Kelola Produk
                    </a>
                </li>

                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('seller.ratings.index') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                        href="{{ route('seller.ratings.index') }}">
                        Kelola Rating
                        </a>
                    </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('seller.orders.index') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                    href="/seller/orders">
                    Pesanan Baru
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('seller.rentals.index') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                    href="/seller/rentals">
                    Penyewaan Alat
                    </a>
                </li>

                {{-- CHAT TETAP DI ATAS --}}
                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('SellerView.chat.index') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                    href="/seller/chat">
                    Chat Pembeli
                    </a>
                </li>

            </ul>
        </div>

        {{-- BOTTOM --}}
        <div class="px-3 pb-4">
            <hr>

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('SellerView.store-profile.show') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                    href="/seller/store-profile/show">
                        Profil Toko
                    </a>
                </li>
            </ul>
        </div>

    </div>

    {{-- MAIN --}}
    <div class="flex-grow-1 p-4">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">DASHBOARD</h4>
        </div>

        {{-- CARDS --}}
        <div class="row g-3 mb-4">

            {{-- Pendapatan --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3" style="border-radius:16px;">
                    <small class="text-muted">Pendapatan (bulan ini)</small>
                    <h4 class="fw-bold mt-1">Rp {{ number_format($totalRevenue,0,',','.') }}</h4>
                    <small class="text-success">pendapatan meningkat dari bulan lalu</small>
                </div>
            </div>

            {{-- Pesanan --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3" style="border-radius:16px;">
                    <small class="text-muted">Pesanan Masuk</small>
                    <h4 class="fw-bold mt-1">{{ $orders->count() }}</h4>
                    <small class="text-danger">{{ $pendingOrders }} Perlu dikirim</small>
                </div>
            </div>

            {{-- Sewa --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3" style="border-radius:16px;">
                    <small class="text-muted">Alat Sedang Disewa</small>
                    <h4 class="fw-bold mt-1">{{ $products->where('kategori','sewa')->count() }}</h4>
                    <small class="text-primary">Menunggu konfirmasi</small>
                </div>
            </div>

            {{-- Rating --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3" style="border-radius:16px;">
                    <small class="text-muted">Rating Seller</small>
                    <h4 class="fw-bold mt-1">{{ number_format($avgStoreRating, 1) }} <small class="text-muted">/5.0</small></h4>
                    <small class="text-success">{{ $storeRatingCount }} ulasan</small>
                </div>
            </div>

        </div>

        <div class="row g-4">

            {{-- LEFT --}}
            <div class="col-md-8">
                <div class="card border-0 shadow-sm p-3" style="border-radius:16px;">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">KONFIRMASI PESANAN & RESI</h6>
                        <small class="text-success fw-semibold">LIHAT SEMUA</small>
                    </div>

                    <table class="table align-middle">
                        <thead class="text-muted small">
                            <tr>
                                <th>ID PESANAN</th>
                                <th>PRODUK</th>
                                <th>STATUS</th>
                                <th>AKSI</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($orders->take(5) as $o)
                            <tr>
                                <td class="fw-semibold">#ORD-{{ $o->id }}</td>
                                <td>{{ $o->product->nama_produk ?? '-' }}</td>

                                <td>
                                @if($o->status == 'diproses')
                                    <span class="badge bg-primary">Diproses</span>
                                @elseif($o->status == 'dikirim')
                                    <span class="badge bg-warning text-dark">Dikirim</span>
                                @elseif($o->status == 'selesai')
                                    <span class="badge bg-success">Selesai</span>
                                @else
                                    <span class="badge bg-secondary">{{ $o->status }}</span>
                                @endif
                            </td>

                                <td>
                                    @if(empty($o->resi))
                                        {{-- Jika belum ada resi --}}
                                        <form action="/seller/orders/{{ $o->id }}/update-resi" method="POST" class="d-flex gap-2">
                                            @csrf

                                            <input type="text"
                                                name="resi"
                                                class="form-control form-control-sm rounded-pill"
                                                placeholder="Masukkan resi..."
                                                required>

                                            <button type="submit"
                                                    class="btn btn-success btn-sm rounded-pill px-3">
                                                Simpan
                                            </button>
                                        </form>
                                    @else
                                        {{-- Jika sudah ada resi --}}
                                        <span class="badge bg-light text-dark border rounded-pill px-3 py-2">
                                            No: {{ $o->resi }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada pesanan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>

            {{-- RIGHT --}}
            <div class="col-md-4">

                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius:16px;">
                    <h6 class="fw-bold">KONFIRMASI SEWA</h6>
                    <p class="text-muted small mb-2">Belum ada permintaan baru.</p>
                </div>

                <div class="card border-0 text-white p-3"
                     style="border-radius:16px;
                            background: linear-gradient(135deg, #10B981, #065F46);">
                    <h6 class="fw-bold">BUTUH BANTUAN?</h6>
                    <p class="small">Dapatkan tips mengelola rental alat outdoor di Campify.</p>
                    <button class="btn btn-light btn-sm rounded-pill">
                        Pelajari Selengkapnya
                    </button>
                </div>

            </div>

        </div>

    </div>
</div>
@endsection

