@extends('SellerView.layouts.app_seller')

@section('content')
<div class="d-flex" style="min-height:100vh; background:#f9fafb;">

    {{-- SIDEBAR --}}
    <div style="width:260px; background:white; border-right:1px solid #eee; display:flex; flex-direction:column; justify-content:space-between;">
        
        <div>
            <div class="p-4">
                <h4 style="color:#10B981; font-weight:800;">CAMPIFY.</h4>
                <small class="text-muted">SELLER HUB</small>
            </div>

            <ul class="nav flex-column px-3">

                <li class="nav-item mb-2">
                    <a class="nav-link text-dark" href="/seller/dashboard">Dashboard</a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link text-dark" href="/seller/products">Kelola Produk</a>
                </li>

                    <li class="nav-item mb-2">
                        <a class="nav-link text-dark" href="/seller/ratings">Kelola Rating</a>
                    </li>

                <li class="nav-item mb-2">
                    <a class="nav-link text-dark" href="/seller/orders">
                        Pesanan Baru
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link text-dark" href="/seller/rentals">
                        Penyewaan Alat
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link text-dark" href="/seller/chat">Chat Pembeli</a>
                </li>

            </ul>
        </div>

        <div class="px-3 pb-4">
            <hr>
            <a class="nav-link bg-success text-white rounded px-3 py-2" href="/seller/store-profile/show">Profil Toko</a>
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

