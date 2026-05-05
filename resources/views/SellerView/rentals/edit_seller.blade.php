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
                <a class="nav-link {{ request()->routeIs('products.index') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}"
                   href="{{ route('seller.products.index') }}">
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
            <h4 class="fw-bold">UBAH STATUS PENYEWAAAN</h4>
            <a href="/seller/rentals" class="btn btn-light rounded-pill px-3">← Kembali</a>
        </div>

        <div class="card border-0 shadow-sm p-4" style="border-radius:16px;">

            <h5 class="fw-bold mb-1">{{ $rental->user->name ?? '-' }}</h5>
            <p class="text-muted small mb-3">Alat: <strong>{{ optional($rental->product)->nama_produk ?? '-' }}</strong></p>

            <form method="POST" action="/seller/rentals/{{ $rental->id }}">
                @csrf
                @method('PUT')

                <label class="form-label fw-bold">Status Penyewaan</label>
                <select name="status" class="form-control mb-3">
                    <option value="pending" {{ $rental->status == 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="confirmed" {{ $rental->status == 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                    <option value="active" {{ $rental->status == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="completed" {{ $rental->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ $rental->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>

                <label class="form-label fw-bold">Catatan</label>
                <textarea name="catatan" class="form-control mb-3" rows="3" placeholder="Tambahkan catatan jika diperlukan">{{ $rental->catatan ?? '' }}</textarea>

                <button class="btn text-white rounded-pill px-4" style="background:#10B981;">
                    Simpan Perubahan
                </button>
            </form>

        </div>

    </div>

</div>
@endsection

