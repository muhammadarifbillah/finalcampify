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

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">DETAIL PESANAN</h4>
            <a href="/seller/orders" class="btn btn-light rounded-pill px-3">← Kembali</a>
        </div>

        {{-- CARD --}}
        <div class="card border-0 shadow-sm p-4" style="border-radius:16px;">

            <div class="row">

                {{-- IMAGE --}}
                <div class="col-md-4">
                    @if(optional($order->product)->gambar)
                        <img src="{{ asset('storage/' . $order->product->gambar) }}"
                             class="img-fluid rounded"
                             style="height:250px; object-fit:cover; width:100%;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center rounded"
                             style="height:250px;">
                            <span class="text-muted">No Image</span>
                        </div>
                    @endif
                </div>

                {{-- DETAIL --}}
                <div class="col-md-8">

                    <h5 class="fw-bold mb-1">{{ $order->buyer_name }}</h5>

                    <p class="text-muted small mb-2">
                        {{ optional($order->product)->nama_produk ?? '-' }}
                    </p>

                    {{-- STATUS --}}
                    <span class="badge 
                        @if ($order->status == 'pending') bg-warning text-dark
                        @elseif ($order->status == 'processing') bg-primary
                        @elseif ($order->status == 'shipped') bg-info text-dark
                        @elseif ($order->status == 'completed') bg-success
                        @else bg-secondary
                        @endif
                        mb-3">

                        @switch($order->status)
                            @case('pending') Menunggu @break
                            @case('processing') Diproses @break
                            @case('shipped') Dikirim @break
                            @case('completed') Selesai @break
                            @default {{ $order->status }}
                        @endswitch

                    </span>

                    <hr>

                    <p><strong>Harga:</strong><br>
                        Rp {{ number_format(optional($order->product)->harga ?? 0,0,',','.') }}
                    </p>

                    <p><strong>Deskripsi Produk:</strong><br>
                        {{ optional($order->product)->deskripsi ?? '-' }}
                    </p>

                    {{-- RESI --}}
                    @if($order->resi)
                        <p><strong>No Resi:</strong> {{ $order->resi }}</p>
                    @endif

                    {{-- BUKTI PEMBAYARAN --}}
                    @if($order->bukti_pembayaran)
                        <div class="mt-4 p-3 bg-light rounded border">
                            <p class="small fw-bold text-muted mb-2">BUKTI PEMBAYARAN</p>
                            <a href="{{ asset($order->bukti_pembayaran) }}" target="_blank">
                                <img src="{{ asset($order->bukti_pembayaran) }}" class="img-thumbnail" style="max-height:150px;">
                            </a>
                            <div class="mt-2">
                                <small class="text-muted italic">*Klik gambar untuk memperbesar</small>
                            </div>
                        </div>
                    @endif

                    {{-- ACTION --}}
                    <div class="mt-4">
                        <a href="/seller/orders/{{ $order->id }}/edit" 
                           class="btn text-white rounded-pill px-4"
                           style="background:#10B981;">
                           Ubah Status
                        </a>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection

