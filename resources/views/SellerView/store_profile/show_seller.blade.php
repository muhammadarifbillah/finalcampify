@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold m-0 text-dark">Profil Toko</h2>
            <p class="text-muted">Ringkasan identitas dan performa bisnis Anda di Campify.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="/seller/store-profile" class="btn btn-emerald px-4 rounded-pill shadow-sm">
                <i class="bi bi-pencil-square me-2"></i>Edit Profil
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="row g-4">
    {{-- LEFT: PROFILE CARD --}}
    <div class="col-lg-4">
        <div class="card card-modern border-0 overflow-hidden mb-4 shadow-lg">
            <div class="p-5 text-center" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
                <div class="profile-avatar mx-auto mb-4 d-flex align-items-center justify-content-center text-white fw-bold rounded-circle border border-4 border-white border-opacity-10 shadow" 
                     style="width: 120px; height: 120px; background: var(--primary-emerald); font-size: 3rem;">
                    {{ substr($profile->nama_toko ?? auth()->user()->name, 0, 1) }}
                </div>
                <h4 class="fw-bold text-white mb-1">{{ $profile->nama_toko ?? 'Toko Saya' }}</h4>
                <p class="text-white-50 small mb-0">{{ auth()->user()->email }}</p>
            </div>
            <div class="card-body p-4 bg-white">
                <div class="d-flex justify-content-center gap-2 mb-4">
                    <span class="badge rounded-pill bg-emerald-soft text-emerald px-3 py-2 border border-emerald">
                        <i class="bi bi-check-circle-fill me-1"></i> Terverifikasi
                    </span>
                    <span class="badge rounded-pill bg-light text-muted px-3 py-2 border">
                        Seller Hub
                    </span>
                </div>
                
                <div class="row text-center border-top pt-4">
                    <div class="col-6 border-end">
                        <h4 class="fw-bold m-0 text-dark">{{ number_format($avgRating, 1) }}</h4>
                        <small class="text-muted">Rating Toko</small>
                    </div>
                    <div class="col-6">
                        <h4 class="fw-bold m-0 text-dark">{{ $ratingCount }}</h4>
                        <small class="text-muted">Ulasan</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- BANK INFO CARD --}}
        <div class="card card-modern border-0 p-4 mb-4">
            <h6 class="fw-bold mb-4 small text-muted text-uppercase ls-1">Informasi Pembayaran</h6>
            <div class="p-3 bg-light rounded-4 mb-3">
                <small class="text-muted d-block mb-1">Nama Bank</small>
                <span class="fw-bold text-dark">{{ $profile->bank_name ?? '-' }}</span>
            </div>
            <div class="p-3 bg-light rounded-4 mb-3">
                <small class="text-muted d-block mb-1">Nomor Rekening</small>
                <span class="fw-bold text-dark">{{ $profile->bank_account_number ?? '-' }}</span>
            </div>
            <div class="p-3 bg-light rounded-4">
                <small class="text-muted d-block mb-1">Atas Nama</small>
                <span class="fw-bold text-dark">{{ $profile->bank_account_name ?? '-' }}</span>
            </div>
        </div>
    </div>

    {{-- RIGHT: STATS & LATEST PRODUCTS --}}
    <div class="col-lg-8">
        {{-- QUICK STATS --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card card-modern border-0 p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 bg-emerald-soft text-emerald rounded-4"><i class="bi bi-box-seam fs-4"></i></div>
                        <div>
                            <h4 class="fw-bold m-0 text-dark">{{ $totalProducts }}</h4>
                            <small class="text-muted">Total Produk</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-modern border-0 p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-4"><i class="bi bi-calendar-check fs-4"></i></div>
                        <div>
                            <h4 class="fw-bold m-0 text-dark">{{ $rentalProducts }}</h4>
                            <small class="text-muted">Produk Sewa</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-modern border-0 p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4"><i class="bi bi-star fs-4"></i></div>
                        <div>
                            <h4 class="fw-bold m-0 text-dark">{{ number_format($avgRating, 1) }}</h4>
                            <small class="text-muted">Rata-rata Rating</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ABOUT SECTION --}}
        <div class="card card-modern border-0 p-5 mb-4">
            <h5 class="fw-bold mb-4">Tentang Toko</h5>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="fw-bold small text-muted text-uppercase ls-1 d-block mb-2">Deskripsi Toko</label>
                    <p class="text-dark leading-relaxed mb-0">{{ $profile->deskripsi ?? 'Belum ada deskripsi untuk toko ini.' }}</p>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold small text-muted text-uppercase ls-1 d-block mb-2">Informasi Kontak</label>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-telephone text-emerald"></i>
                        <span class="text-dark small">{{ $profile->no_telp ?? '-' }}</span>
                    </div>
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-geo-alt text-emerald mt-1"></i>
                        <span class="text-dark small leading-relaxed">{{ $profile->alamat ?? 'Lokasi belum diatur' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- LATEST PRODUCTS GRID --}}
        <h5 class="fw-bold mb-4">Koleksi Produk Terbaru</h5>
        @php
            $latestProducts = \App\Models\SellerModels\Product_seller::where('user_id', auth()->id())->latest()->take(3)->get();
        @endphp
        <div class="row g-4">
            @forelse($latestProducts as $p)
            <div class="col-md-4">
                <div class="card card-modern border-0 overflow-hidden h-100">
                    <div style="height: 150px; background: #f8fafc;">
                        @if($p->gambar && file_exists(public_path('storage/'.$p->gambar)))
                            <img src="{{ asset('storage/'.$p->gambar) }}" class="w-100 h-100 object-fit-cover">
                        @else
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center opacity-25 fs-2">🏕️</div>
                        @endif
                    </div>
                    <div class="card-body p-3">
                        <h6 class="fw-bold text-dark text-truncate mb-1">{{ $p->nama_produk }}</h6>
                        <span class="text-emerald fw-bold small">Rp {{ number_format($p->harga, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card card-modern border-0 p-5 text-center text-muted">
                    <p class="mb-0">Belum ada produk terdaftar.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .bg-emerald-soft { background-color: #ecfdf5; }
    .border-emerald { border: 1px solid #10B981 !important; }
    .object-fit-cover { object-fit: cover; }
    .leading-relaxed { line-height: 1.6; }
</style>
@endsection
