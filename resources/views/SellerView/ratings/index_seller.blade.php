@extends('SellerView.layouts.app_seller')

@section('content')
<div class="d-flex" style="min-height:100vh; background:#f9fafb;">
    {{-- SIDEBAR --}}
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
                    <a class="nav-link {{ request()->routeIs('products*') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                    href="{{ route('products.index') }}">
                    Kelola Produk
                    </a>
                </li>

                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('ratings.index') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                        href="{{ route('ratings.index') }}">
                        Kelola Rating
                        </a>
                    </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('orders*') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                    href="/seller/orders">
                    Pesanan Baru
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('rentals.index') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                    href="/seller/rentals">
                    Penyewaan Alat
                    </a>
                </li>

                {{-- CHAT TETAP DI ATAS --}}
                <li class="nav-item mb-2">
                    <a class="nav-link {{ request()->routeIs('chat.index') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
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
                    <a class="nav-link {{ request()->routeIs('store-profile*') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                    href="/store-profile">
                        Profil Toko
                    </a>
                </li>
            </ul>
        </div>

    </div>

    {{-- MAIN CONTENT --}}
    <div class="flex-grow-1 p-4">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">KELOLA RATING & ULASAN</h4>

            <div>
                <button class="btn btn-light rounded-pill px-3">
                    Total Ulasan: {{ $totalReviews }}
                </button>
            </div>
        </div>

        {{-- KPI --}}
        <div class="row g-3 mb-4">

            {{-- Rating Seller --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3" style="border-radius:16px;">
                    <small class="text-muted">Rating Seller</small>
                    <h4 class="fw-bold mt-1">{{ $averageRating }}/5.0</h4>
                    <small class="text-success">Gabungan toko & produk</small>
                </div>
            </div>

            {{-- Rating Produk --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3" style="border-radius:16px;">
                    <small class="text-muted">Rating Produk</small>
                    <h4 class="fw-bold mt-1">{{ $avgProductRating }}/5.0</h4>
                    <small class="text-primary">{{ $totalProductReviews }} ulasan</small>
                </div>
            </div>

            {{-- Rating Toko --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3" style="border-radius:16px;">
                    <small class="text-muted">Rating Toko</small>
                    <h4 class="fw-bold mt-1">{{ $avgStoreRating }}/5.0</h4>
                    <small class="text-warning">{{ $totalStoreReviews }} ulasan</small>
                </div>
            </div>

            {{-- Bintang 5 --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm p-3" style="border-radius:16px;">
                    <small class="text-muted">Ulasan Bintang 5</small>
                    <h4 class="fw-bold mt-1">{{ $fiveStar }}</h4>
                    <small class="text-success">Review terbaik</small>
                </div>
            </div>

        </div>

        <div class="row g-4">

            {{-- LEFT CONTENT --}}
            <div class="col-md-8">

                {{-- ULASAN PRODUK --}}
                <div class="card border-0 shadow-sm p-3 mb-4" style="border-radius:16px;">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">ULASAN PRODUK</h6>
                    </div>

                    @forelse($productRatings as $rating)
                    <div class="border rounded-4 p-3 mb-3">

                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="fw-bold mb-1">{{ $rating->user->name ?? 'User' }}</h6>
                                <small class="text-muted">
                                    {{ $rating->product->nama_produk ?? 'Produk Campify' }}
                                </small>
                            </div>

                            <span class="badge bg-success">
                                ⭐ {{ $rating->rating }}/5
                            </span>
                        </div>

                        <p class="text-muted small mt-2 mb-0">
                            {{ $rating->ulasan ?? 'Tidak ada ulasan.' }}
                        </p>

                    </div>
                    @empty
                    <p class="text-muted">Belum ada ulasan produk.</p>
                    @endforelse

                </div>

                {{-- ULASAN TOKO --}}
                <div class="card border-0 shadow-sm p-3" style="border-radius:16px;">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">ULASAN TOKO</h6>
                    </div>

                    @forelse($storeRatings as $rating)
                    <div class="border rounded-4 p-3 mb-3">

                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="fw-bold mb-1">{{ $rating->user->name ?? 'User' }}</h6>
                                <small class="text-muted">
                                    Review untuk toko
                                </small>
                            </div>

                            <span class="badge bg-primary">
                                ⭐ {{ $rating->rating }}/5
                            </span>
                        </div>

                        <p class="text-muted small mt-2 mb-0">
                            {{ $rating->ulasan ?? 'Tidak ada ulasan.' }}
                        </p>

                    </div>
                    @empty
                    <p class="text-muted">Belum ada ulasan toko.</p>
                    @endforelse

                </div>

            </div>

            {{-- RIGHT PANEL --}}
            <div class="col-md-4">

                {{-- DISTRIBUSI BINTANG --}}
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius:16px;">
                    <h6 class="fw-bold mb-3">DISTRIBUSI RATING</h6>

                    <p class="mb-2">⭐⭐⭐⭐⭐ : {{ $fiveStar }}</p>
                    <p class="mb-2">⭐⭐⭐⭐ : {{ $fourStar }}</p>
                    <p class="mb-2">⭐⭐⭐ : {{ $threeStar }}</p>
                    <p class="mb-2">⭐⭐ : {{ $twoStar }}</p>
                    <p class="mb-0">⭐ : {{ $oneStar }}</p>
                </div>

                {{-- BANTUAN --}}
                <div class="card border-0 text-white p-3"
                     style="border-radius:16px;
                            background: linear-gradient(135deg, #10B981, #065F46);">

                    <h6 class="fw-bold">TINGKATKAN RATING TOKO</h6>

                    <p class="small">
                        Balas cepat chat pembeli, kirim tepat waktu, dan jaga kualitas produk outdoor Anda.
                    </p>

                    <button class="btn btn-light btn-sm rounded-pill">
                        Pelajari Tips
                    </button>

                </div>

            </div>

        </div>

    </div>
</div>
@endsection