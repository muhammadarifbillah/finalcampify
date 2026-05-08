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
                    <h5 class="fw-bold mb-1">{{ $rental->user->name ?? '-' }}</h5>
                    <p class="text-muted small mb-3">{{ optional($rental->product)->nama_produk ?? '-' }}</p>

                    <span class="badge 
                        @switch($rental->status)
                            @case('pending') bg-warning text-dark @break
                            @case('confirmed') bg-info text-dark @break
                            @case('active') bg-primary @break
                            @case('completed') bg-success @break
                            @case('cancelled') bg-danger @break
                            @default bg-secondary
                        @endswitch
                        mb-3">
                        @switch($rental->status)
                            @case('pending') Menunggu @break
                            @case('confirmed') Dikonfirmasi @break
                            @case('active') Aktif @break
                            @case('completed') Selesai @break
                            @case('cancelled') Dibatalkan @break
                            @default {{ $rental->status }}
                        @endswitch
                    </span>

                    <hr>

                    <p><strong>Tanggal Mulai:</strong> {{ \Carbon\Carbon::parse($rental->start_date)->format('d F Y') }}</p>
                    <p><strong>Tanggal Selesai:</strong> {{ \Carbon\Carbon::parse($rental->end_date)->format('d F Y') }}</p>
                    <p><strong>Total Harga:</strong> Rp {{ number_format($rental->price,0,',','.') }}</p>

                    @if($rental->catatan)
                        <p><strong>Catatan:</strong> {{ $rental->catatan }}</p>
                    @endif

                    {{-- BUKTI PEMBAYARAN --}}
                    @if($rental->order && $rental->order->bukti_pembayaran)
                        <div class="mt-4 p-3 bg-light rounded border">
                            <p class="small fw-bold text-muted mb-2">BUKTI PEMBAYARAN SEWA</p>
                            <a href="{{ asset($rental->order->bukti_pembayaran) }}" target="_blank">
                                <img src="{{ asset($rental->order->bukti_pembayaran) }}" class="img-thumbnail" style="max-height:150px;">
                            </a>
                        </div>
                    @endif

                    {{-- DETAIL PENGEMBALIAN (RETURN) --}}
                    @if($rental->returnRequest)
                        <div class="mt-4 p-4 rounded border" style="background:#f0fdf4; border-color:#dcfce7 !important;">
                            <p class="small fw-bold text-success mb-3 uppercase tracking-wider">Informasi Pengembalian (Return)</p>
                            
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="text-muted small d-block">No. Resi Balik</label>
                                    <span class="fw-bold">{{ $rental->returnRequest->resi_return }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <label class="text-muted small d-block">Tanggal Kembali</label>
                                    <span class="fw-bold">{{ $rental->returnRequest->tanggal_pengembalian ? $rental->returnRequest->tanggal_pengembalian->format('d F Y H:i') : '-' }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <label class="text-muted small d-block">Kondisi Barang</label>
                                    <span class="badge bg-white text-dark border uppercase">{{ $rental->returnRequest->kondisi_barang }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <label class="text-muted small d-block">Nominal Denda</label>
                                    <span class="fw-bold text-danger">Rp {{ number_format($rental->returnRequest->denda) }}</span>
                                </div>
                            </div>

                            @if($rental->returnRequest->foto_kondisi)
                                <div class="mt-3 pt-3 border-top">
                                    <label class="text-muted small d-block mb-2">Bukti Kondisi Barang (Dari Pembeli)</label>
                                    @php
                                        $ext = pathinfo($rental->returnRequest->foto_kondisi, PATHINFO_EXTENSION);
                                        $isVideo = in_array(strtolower($ext), ['mp4', 'mov', 'avi']);
                                    @endphp

                                    @if($isVideo)
                                        <video width="100%" height="auto" controls class="rounded">
                                            <source src="{{ asset('storage/' . $rental->returnRequest->foto_kondisi) }}" type="video/{{ $ext }}">
                                            Your browser does not support the video tag.
                                        </video>
                                    @else
                                        <a href="{{ asset('storage/' . $rental->returnRequest->foto_kondisi) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $rental->returnRequest->foto_kondisi) }}" class="img-thumbnail" style="max-height:200px;">
                                        </a>
                                    @endif
                                </div>
                            @endif

                            @if($rental->returnRequest->bukti_denda)
                                <div class="mt-3 pt-3 border-top">
                                    <label class="text-muted small d-block mb-2">Bukti Pembayaran Denda</label>
                                    <a href="{{ asset('storage/' . $rental->returnRequest->bukti_denda) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $rental->returnRequest->bukti_denda) }}" class="img-thumbnail" style="max-height:120px;">
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="/seller/rentals/{{ $rental->id }}/edit" class="btn text-white rounded-pill px-4" style="background:#10B981;">
                            Ubah Status
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

