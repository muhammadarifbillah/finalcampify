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
        </div>

        <div class="card border-0 shadow-sm p-4" style="border-radius:16px;">

            <form method="POST" action="/seller/store-profile">
                @csrf
                @method('POST')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Toko</label>
                            <input type="text" name="nama_toko" class="form-control" 
                                   value="{{ $profile->nama_toko ?? '' }}" 
                                   placeholder="Masukkan nama toko" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">No. Telepon</label>
                            <input type="text" name="no_telp" class="form-control" 
                                   value="{{ $profile->no_telp ?? '' }}" 
                                   placeholder="Masukkan nomor telepon">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alamat</label>
                            <input type="text" name="alamat" class="form-control" 
                                   value="{{ $profile->alamat ?? '' }}" 
                                   placeholder="Masukkan alamat toko">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Deskripsi Toko</label>
                            <textarea name="deskripsi" class="form-control" rows="3" 
                                      placeholder="Deskripsi tentang toko Anda">{{ $profile->deskripsi ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <button class="btn text-white rounded-pill px-4" style="background:#10B981;">
                    Simpan Profil
                </button>
            </form>

        </div>

        {{-- RATING TOKO --}}
        <div class="card border-0 shadow-sm p-4 mt-4" style="border-radius:16px;">
            <h6 class="fw-bold mb-3">RATING TOKO</h6>
            
            @php
                $storeId = \Illuminate\Support\Facades\Auth::id();
                $avgStoreRating = \App\Models\SellerModels\StoreRating_seller::getAverageRating($storeId);
                $storeRatingCount = \App\Models\SellerModels\StoreRating_seller::getRatingCount($storeId);
                $storeRatings = \App\Models\SellerModels\StoreRating_seller::where('store_id', $storeId)
                    ->with('user:id,name')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
            @endphp

            <div class="d-flex align-items-center mb-4">
                <h2 class="fw-bold me-2">{{ number_format($avgStoreRating, 1) }}</h2>
                <div>
                    <div>
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($avgStoreRating))
                                <span style="color:#F59E0B; font-size:20px;">★</span>
                            @else
                                <span style="color:#D1D5DB; font-size:20px;">★</span>
                            @endif
                        @endfor
                    </div>
                    <small class="text-muted">{{ $storeRatingCount }} ulasan</small>
                </div>
            </div>

            @forelse($storeRatings as $sr)
            <div class="border-bottom py-3">
                <div class="d-flex justify-content-between">
                    <strong>{{ $sr->user->name ?? 'User' }}</strong>
                    <small class="text-muted">{{ $sr->created_at->diffForHumans() }}</small>
                </div>
                <div class="mb-1">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $sr->rating)
                            <span style="color:#F59E0B;">★</span>
                        @else
                            <span style="color:#D1D5DB;">★</span>
                        @endif
                    @endfor
                </div>
                <p class="mb-0 text-muted small">{{ $sr->ulasan ?? '-' }}</p>
            </div>
            @empty
            <p class="text-muted">Belum ada rating untuk toko ini.</p>
            @endforelse

        </div>

    </div>

</div>
@endsection

