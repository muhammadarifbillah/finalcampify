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
            <h4 class="fw-bold">DETAIL PENYEWAAAN</h4>
            <a href="/seller/rentals" class="btn btn-light rounded-pill px-3">← Kembali</a>
        </div>

        <div class="card border-0 shadow-sm p-4" style="border-radius:16px;">

            <div class="row">
                <div class="col-md-4">
                    @if(optional($rental->product)->gambar)
                        <img src="{{ asset('storage/' . $rental->product->gambar) }}" class="img-fluid rounded" style="height:200px; object-fit:cover; width:100%;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height:200px;">
                            <span class="text-muted">No Image</span>
                        </div>
                    @endif
                </div>
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h5 class="fw-bold mb-0">{{ $rental->user->name ?? '-' }}</h5>
                        @if($rental->user->ktp_verified_at)
                            <span class="badge bg-primary rounded-pill" style="font-size: 10px;" title="User Terverifikasi KTP">
                                <i class="fas fa-check-circle"></i> Terverifikasi
                            </span>
                        @else
                            <span class="badge bg-secondary rounded-pill" style="font-size: 10px;" title="Belum Verifikasi KTP">
                                Belum Verifikasi
                            </span>
                        @endif
                    </div>
                    <p class="text-muted small mb-3">{{ optional($rental->product)->nama_produk ?? '-' }}</p>

                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge 
                            @switch($rental->status)
                                @case('pending') bg-warning text-dark @break
                                @case('confirmed') bg-info text-dark @break
                                @case('active') bg-primary @break
                                @case('completed') bg-success @break
                                @case('cancelled') bg-danger @break
                                @default bg-secondary
                            @endswitch">
                            @switch($rental->status)
                                @case('pending') Menunggu @break
                                @case('confirmed') Dikonfirmasi @break
                                @case('active') Aktif @break
                                @case('completed') Selesai @break
                                @case('cancelled') Dibatalkan @break
                                @default {{ $rental->status }}
                            @endswitch
                        </span>

                        @if(!$rental->user->ktp_verified_at)
                            <span class="badge bg-danger animate-pulse" style="font-size: 10px;">
                                ⚠️ RISIKO TINGGI
                            </span>
                        @endif
                    </div>

                    @if(!$rental->user->ktp_verified_at)
                        <div class="alert alert-danger border-0 rounded-4 p-3 mb-4">
                            <h6 class="fw-bold mb-1" style="font-size: 13px;"><i class="fas fa-exclamation-triangle"></i> Verifikasi KTP Diperlukan</h6>
                            <p class="small mb-2" style="font-size: 11px;">Anda wajib memvalidasi KTP penyewa sebelum mengonfirmasi penyewaan ini.</p>
                            
                            @if($rental->user->ktp_image)
                                <div class="mt-2">
                                    <a href="{{ asset($rental->user->ktp_image) }}" target="_blank">
                                        <img src="{{ asset($rental->user->ktp_image) }}" class="img-thumbnail mb-2" style="max-height:100px;">
                                    </a>
                                    <form action="{{ route('seller.user.verify', $rental->user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold">Verifikasi KTP</button>
                                    </form>
                                </div>
                            @else
                                <p class="small text-danger italic" style="font-size: 11px;">Penyewa belum mengunggah foto KTP.</p>
                            @endif
                        </div>
                    @endif

                    <hr>

                    <p><strong>Tanggal Mulai:</strong> {{ \Carbon\Carbon::parse($rental->start_date)->format('d F Y') }}</p>
                    <p><strong>Tanggal Selesai:</strong> {{ \Carbon\Carbon::parse($rental->end_date)->format('d F Y') }}</p>
                    
                    <div class="p-3 bg-light rounded border mt-3">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="small text-muted font-bold text-uppercase" style="font-size: 10px;">Pendapatan Sewa</div>
                                <div class="fw-bold">Rp {{ number_format($rental->price, 0, ',', '.') }}</div>
                            </div>
                            <div class="col-6 border-start">
                                <div class="small text-emerald-600 font-bold text-uppercase" style="font-size: 10px;">Dana Jaminan (50%)</div>
                                <div class="fw-bold">Rp {{ number_format($rental->product->buy_price * 0.5, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>

                    @if($rental->catatan)
                        <p><strong>Catatan:</strong> {{ $rental->catatan }}</p>
                    @endif

                    {{-- BUKTI PEMBAYARAN --}}
                    @if($rental->order && $rental->order->bukti_pembayaran)
                        <div class="mt-4 p-3 bg-light rounded border">
                            <p class="small fw-bold text-muted mb-2">BUKTI PEMBAYARAN</p>
                            <a href="{{ asset($rental->order->bukti_pembayaran) }}" target="_blank">
                                <img src="{{ asset($rental->order->bukti_pembayaran) }}" class="img-thumbnail" style="max-height:150px;">
                            </a>
                            <div class="mt-2">
                                <small class="text-muted italic">*Klik gambar untuk memperbesar</small>
                            </div>
                        </div>
                    @endif

                    <div class="mt-4">
                        @if($rental->user->ktp_verified_at)
                            <a href="/seller/rentals/{{ $rental->id }}/edit" class="btn text-white rounded-pill px-4" style="background:#10B981;">
                                Ubah Status
                            </a>
                        @else
                            <button class="btn btn-secondary rounded-pill px-4" disabled title="Harap verifikasi KTP penyewa terlebih dahulu">
                                🔒 Status Terkunci
                            </button>
                        @endif
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

