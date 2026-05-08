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

    {{-- CONTENT --}}
    <div class="flex-grow-1 p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">PROFIL TOKO</h4>
            <a href="/seller/store-profile" class="btn btn-outline-secondary rounded-pill">
                Edit Profil
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success rounded-3 mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="row g-4">
            {{-- INFO UTAMA TOKO --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                    <div class="card-body text-center p-4">
                        {{-- Logo Toko --}}
                        <div style="width:120px; height:120px; background:linear-gradient(135deg, #10B981, #065F46); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px;">
                            @if($profile && $profile->logo && file_exists(public_path('storage/'.$profile->logo)))
                                <img src="{{ asset('storage/'.$profile->logo) }}" alt="Logo" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                            @else
                                <span style="font-size:48px; color:white;">🏕️</span>
                            @endif
                        </div>

                        <h4 class="fw-bold mb-1">{{ $profile->nama_toko ?? 'Toko Anda' }}</h4>
                        <p class="text-muted small mb-3">Outdoor Equipment Store</p>

                        <div class="d-flex justify-content-center gap-2 mb-3">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($avgRating))
                                    <span style="color:#F59E0B; font-size:20px;">★</span>
                                @else
                                    <span style="color:#D1D5DB; font-size:20px;">★</span>
                                @endif
                            @endfor
                            <span class="text-muted small">({{ number_format($avgRating, 1) }} / 5.0)</span>
                        </div>

                        <a href="/seller/store-profile" class="btn btn-success rounded-pill px-4 w-100">
                            Edit Profil
                        </a>
                    </div>
                </div>
            </div>

            {{-- STATISTIK & DETAIL --}}
            <div class="col-md-8">
                {{-- STATISTIK --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm p-3" style="border-radius:16px;">
                            <small class="text-muted">Total Produk</small>
                            <h4 class="fw-bold mt-1">{{ $totalProducts }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm p-3" style="border-radius:16px;">
                            <small class="text-muted">Produk Sewa</small>
                            <h4 class="fw-bold mt-1">{{ $rentalProducts }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm p-3" style="border-radius:16px;">
                            <small class="text-muted">Rating Toko</small>
                            <h4 class="fw-bold mt-1">{{ number_format($avgRating, 1) }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm p-3" style="border-radius:16px;">
                            <small class="text-muted">Total Ulasan</small>
                            <h4 class="fw-bold mt-1">{{ $ratingCount }}</h4>
                        </div>
                    </div>
                </div>

                {{-- DETAIL TOKO --}}
                <div class="card border-0 shadow-sm p-4" style="border-radius:16px;">
                    <h6 class="fw-bold mb-3">Informasi Toko</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <small class="text-muted d-block">Deskripsi</small>
                                <p class="mb-0">{{ $profile->deskripsi ?? 'Belum ada deskripsi' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <small class="text-muted d-block">Alamat</small>
                                <p class="mb-0">{{ $profile->alamat ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <small class="text-muted d-block">Nomor Telepon</small>
                                <p class="mb-0">{{ $profile->no_telp ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <small class="text-muted d-block">Status Toko</small>
                                <span class="badge bg-success rounded-pill">Aktif</span>
                            </div>
                        </div>
                        
                        <div class="col-md-12 mt-2">
                            <hr>
                            <h6 class="fw-bold mb-3">Informasi Rekening Bank</h6>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <small class="text-muted d-block">Nama Bank</small>
                                <p class="mb-0 fw-bold">{{ $profile->bank_name ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <small class="text-muted d-block">Nomor Rekening</small>
                                <p class="mb-0 fw-bold">{{ $profile->bank_account_number ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <small class="text-muted d-block">Atas Nama</small>
                                <p class="mb-0 fw-bold">{{ $profile->bank_account_name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PRODUK TERBARU --}}
                <div class="card border-0 shadow-sm p-4 mt-4" style="border-radius:16px;">
                    <h6 class="fw-bold mb-3">Produk Terbaru</h6>
                    
                    @php
                        $latestProducts = \App\Models\SellerModels\Product_seller::where('user_id', \Illuminate\Support\Facades\Auth::id())->latest()->take(4)->get();
                    @endphp
                    
                    <div class="row g-3">
                        @forelse($latestProducts as $p)
                        <div class="col-md-3">
                            <div class="card border-0" style="border-radius:12px; overflow:hidden;">
                                <div style="height:80px; background:#f3f4f6; display:flex; align-items:center; justify-content:center;">
                                    @if($p->gambar && file_exists(public_path('storage/'.$p->gambar)))
                                        <img src="{{ asset('storage/'.$p->gambar) }}" style="width:100%; height:100%; object-fit:cover;">
                                    @else
                                        <span style="font-size:32px;">🏕️</span>
                                    @endif
                                </div>
                                <div class="card-body p-2">
                                    <p class="mb-0 small fw-bold text-truncate">{{ $p->nama_produk }}</p>
                                    <p class="mb-0 small text-success">Rp {{ number_format($p->harga,0,',','.') }}</p>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center text-muted py-3">
                            Belum ada produk
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

