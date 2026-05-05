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

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">PENYEWAAAN ALAT</h4>
        </div>

        {{-- TABLE --}}
        <div class="card border-0 shadow-sm" style="border-radius:16px;">
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="py-3">Penyewa</th>
                            <th class="py-3">Alat</th>
                            <th class="py-3">Tanggal Sewa</th>
                            <th class="py-3">Total</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rentals as $rental)
                        <tr>
                            <td class="px-4">#RNT-{{ $rental->id }}</td>
                            <td>{{ $rental->user->name ?? '-' }}</td>
                            <td>{{ optional($rental->product)->nama_produk ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($rental->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($rental->tanggal_selesai)->format('d/m/Y') }}</td>
                            <td>Rp {{ number_format($rental->total_harga,0,',','.') }}</td>
                            <td>
                                @switch($rental->status)
                                    @case('pending')
                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                        @break
                                    @case('confirmed')
                                        <span class="badge bg-info text-dark">Dikonfirmasi</span>
                                        @break
                                    @case('active')
                                        <span class="badge bg-primary">Aktif</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-success">Selesai</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger">Dibatalkan</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $rental->status }}</span>
                                @endswitch
                            </td>
                            <td>
                                <a href="/seller/rentals/{{ $rental->id }}" class="btn btn-success btn-sm">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada penyewaan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection