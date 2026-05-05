@extends('SellerView.layouts.app_seller')

@section('content')
<div class="d-flex" style="min-height:100vh; background:#f9fafb;">

    {{-- SIDEBAR --}}
    <div style="width:260px; background:white; border-right:1px solid #eee;">
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
                <a class="nav-link {{ request()->routeIs('products') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                   href="{{ route('products.index') }}">
                   Kelola Produk
                </a>
            </li>

            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->routeIs('orders*') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                   href="/seller/orders">
                   Pesanan Baru
                </a>
            </li>

            <li class="nav-item mb-2">
                <a class="nav-link {{ request()->routeIs('rentals*') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                   href="/seller/rentals">
                   Penyewaan Alat
                </a>
            </li>

            <li class="nav-item"><a class="nav-link text-dark" href="/seller/store-profile">Profil Toko</a></li>
            <li class="nav-item"><a class="nav-link text-dark" href="/seller/chat">Chat Pembeli</a></li>
        </ul>
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

                    <p><strong>Tanggal Mulai:</strong> {{ \Carbon\Carbon::parse($rental->tanggal_mulai)->format('d F Y') }}</p>
                    <p><strong>Tanggal Selesai:</strong> {{ \Carbon\Carbon::parse($rental->tanggal_selesai)->format('d F Y') }}</p>
                    <p><strong>Total Harga:</strong> Rp {{ number_format($rental->total_harga,0,',','.') }}</p>

                    @if($rental->catatan)
                        <p><strong>Catatan:</strong> {{ $rental->catatan }}</p>
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