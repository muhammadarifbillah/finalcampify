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
                        href="/seller/ratings">
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
                    href="/seller/store-profile">
                        Profil Toko
                    </a>
                </li>
            </ul>
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