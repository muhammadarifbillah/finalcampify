@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold m-0 text-dark">Manajemen Pesanan</h2>
            <p class="text-muted">Kelola semua transaksi penjualan produk masuk di sini.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="badge bg-emerald-soft text-emerald rounded-pill px-4 py-2 fw-bold border border-emerald-soft">
                {{ $orders->count() }} Total Pesanan
            </div>
        </div>
    </div>
</div>

{{-- STATUS TABS --}}
<div class="card card-modern p-3 mb-5 border-0">
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('seller.orders.index') }}" 
           class="btn rounded-pill px-4 py-2 fw-semibold {{ request('status') == null ? 'btn-emerald' : 'btn-light text-muted' }}">
           Semua
        </a>
        <a href="{{ route('seller.orders.index', ['status' => 'menunggu']) }}" 
           class="btn rounded-pill px-4 py-2 fw-semibold {{ request('status') == 'menunggu' ? 'btn-warning text-dark' : 'btn-light text-muted' }}">
           Menunggu Konfirmasi
        </a>
        <a href="{{ route('seller.orders.index', ['status' => 'diproses']) }}" 
           class="btn rounded-pill px-4 py-2 fw-semibold {{ request('status') == 'diproses' ? 'btn-info text-white' : 'btn-light text-muted' }}">
           Sedang Diproses
        </a>
        <a href="{{ route('seller.orders.index', ['status' => 'dikirim']) }}" 
           class="btn rounded-pill px-4 py-2 fw-semibold {{ request('status') == 'dikirim' ? 'btn-primary' : 'btn-light text-muted' }}">
           Dalam Pengiriman
        </a>
        <a href="{{ route('seller.orders.index', ['status' => 'selesai']) }}" 
           class="btn rounded-pill px-4 py-2 fw-semibold {{ request('status') == 'selesai' ? 'btn-emerald' : 'btn-light text-muted' }}">
           Selesai
        </a>
    </div>
</div>

{{-- ORDERS LIST --}}
<div class="orders-container">
    @forelse($orders as $order)
    <div class="card card-modern border-0 mb-4 overflow-hidden">
        <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box bg-light rounded-3 p-3">
                    <i class="bi bi-box-seam fs-4 text-emerald"></i>
                </div>
                <div>
                    <h6 class="fw-bold m-0">Order #{{ $order->id }}</h6>
                    <small class="text-muted">{{ $order->created_at->format('d M Y, H:i') }} • {{ $order->buyer->name ?? 'User' }}</small>
                </div>
            </div>
            <div>
                @php
                    $statusClass = match($order->status) {
                        'menunggu' => 'bg-warning-subtle text-warning',
                        'diproses' => 'bg-info-subtle text-info',
                        'dikirim' => 'bg-primary-subtle text-primary',
                        'selesai' => 'bg-emerald-soft text-emerald',
                        'dibatalkan' => 'bg-danger-subtle text-danger',
                        default => 'bg-light text-dark'
                    };
                @endphp
                <span class="badge rounded-pill px-4 py-2 fw-bold text-uppercase ls-1 {{ $statusClass }}">
                    {{ $order->status }}
                </span>
            </div>
        </div>
        
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-8">
                    <h6 class="fw-bold mb-3 small text-muted text-uppercase ls-1">Item yang dipesan</h6>
                    @foreach($order->details as $detail)
                    <div class="d-flex align-items-center gap-3 mb-3 p-3 bg-light rounded-4">
                        @if($detail->product->image_url)
                            <img src="{{ $detail->product->image_url }}" class="rounded-3 shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
                        @else
                            <div class="bg-white rounded-3 d-flex align-items-center justify-content-center border" style="width: 60px; height: 60px;">📦</div>
                        @endif
                        <div class="flex-grow-1">
                            <h6 class="fw-bold m-0">{{ $detail->product->nama_produk ?? '-' }}</h6>
                            <small class="text-muted">{{ $detail->qty }} unit x Rp {{ number_format($detail->harga, 0, ',', '.') }}</small>
                        </div>
                        <div class="text-end fw-bold text-dark">
                            Rp {{ number_format($detail->harga * $detail->qty, 0, ',', '.') }}
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="col-md-4">
                    <div class="p-4 rounded-4 h-100" style="background: var(--bg-light); border: 1px dashed #cbd5e1;">
                        <h6 class="fw-bold mb-3 small text-muted text-uppercase ls-1">Ringkasan Pembayaran</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small text-muted">Subtotal</span>
                            <span class="small fw-bold">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="small text-muted">Biaya Layanan</span>
                            <span class="small fw-bold">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold text-dark">Total Akhir</span>
                            <span class="h5 fw-bold text-emerald m-0">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="d-grid gap-2">
                            @if($order->status == 'menunggu')
                                <a href="/seller/orders/{{ $order->id }}/edit?status=diproses" class="btn btn-emerald rounded-3 py-2 fw-bold shadow-sm">
                                    Konfirmasi Pesanan
                                </a>
                            @elseif($order->status == 'diproses')
                                <a href="/seller/orders/{{ $order->id }}/edit?status=dikirim" class="btn btn-primary rounded-3 py-2 fw-bold border-0" style="background: #3b82f6;">
                                    Kirim Barang
                                </a>
                            @endif
                            <a href="/seller/orders/{{ $order->id }}" class="btn btn-light rounded-3 py-2 fw-bold text-muted border">
                                Lihat Detail Lengkap
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="card card-modern p-5 text-center border-0 bg-white">
        <div class="mb-4 fs-1 opacity-25">🧾</div>
        <h4 class="fw-bold">Belum Ada Pesanan</h4>
        <p class="text-muted">Semua pesanan produk Anda akan muncul di halaman ini.</p>
    </div>
    @endforelse
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .bg-emerald-soft { background-color: var(--soft-emerald); }
    .bg-warning-subtle { background-color: #fffbeb; }
    .bg-info-subtle { background-color: #f0f9ff; }
    .bg-primary-subtle { background-color: #eff6ff; }
    .bg-danger-subtle { background-color: #fef2f2; }
</style>
@endsection
