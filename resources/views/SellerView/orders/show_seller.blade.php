@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="d-flex align-items-center">
        <a href="{{ route('seller.orders.index') }}" class="btn btn-light rounded-circle p-3 me-4 shadow-sm border-0 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="bi bi-arrow-left fs-4"></i>
        </a>
        <div>
            <h2 class="fw-bold m-0 text-dark">Detail Pesanan #{{ $order->id }}</h2>
            <p class="text-muted">Kelola proses pengiriman dan informasi transaksi pesanan ini.</p>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- LEFT: ORDER ITEMS & CUSTOMER INFO --}}
    <div class="col-lg-8">
        {{-- ITEM LIST --}}
        <div class="card card-modern border-0 p-5 mb-4">
            <h5 class="fw-bold mb-4">Item yang Dipesan</h5>
            @foreach($order->details as $detail)
            <div class="d-flex align-items-center gap-4 mb-4 p-4 bg-light rounded-4 border-0">
                <div class="rounded-3 overflow-hidden shadow-sm" style="width: 100px; height: 100px;">
                    @if($detail->product->gambar)
                        <img src="{{ asset('storage/'.$detail->product->gambar) }}" class="w-100 h-100 object-fit-cover">
                    @else
                        <div class="w-100 h-100 bg-white d-flex align-items-center justify-content-center">📦</div>
                    @endif
                </div>
                <div class="flex-grow-1">
                    <small class="text-emerald fw-bold text-uppercase ls-1" style="font-size: 0.7rem;">{{ $detail->product->kategori ?? 'Umum' }}</small>
                    <h5 class="fw-bold m-0 text-dark">{{ $detail->product->nama_produk ?? '-' }}</h5>
                    <p class="text-muted small mb-0">{{ Str::limit($detail->product->deskripsi, 100) }}</p>
                </div>
                <div class="text-end">
                    <small class="text-muted d-block">Harga x Qty</small>
                    <span class="fw-bold text-dark">Rp {{ number_format($detail->harga, 0, ',', '.') }} x {{ $detail->qty }}</span>
                    <h5 class="fw-bold text-emerald mt-1">Rp {{ number_format($detail->harga * $detail->qty, 0, ',', '.') }}</h5>
                </div>
            </div>
            @endforeach
        </div>

        {{-- CUSTOMER & SHIPPING --}}
        <div class="card card-modern border-0 p-5">
            <h5 class="fw-bold mb-4">Informasi Pengiriman</h5>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="p-3 bg-light rounded-circle text-emerald fs-4"><i class="bi bi-person"></i></div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold ls-1">Nama Pembeli</small>
                            <h6 class="fw-bold m-0 text-dark">{{ $order->buyer->name ?? $order->buyer_name }}</h6>
                            <p class="text-muted small m-0">{{ $order->buyer->email ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="p-3 bg-light rounded-circle text-emerald fs-4"><i class="bi bi-geo-alt"></i></div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold ls-1">Alamat Tujuan</small>
                            <p class="text-dark small m-0 fw-semibold">
                                {{ $order->shipping_address ?? 'Alamat tidak tersedia' }}<br>
                                {{ $order->shipping_district ?? '' }}, {{ $order->shipping_city ?? '' }} {{ $order->shipping_postal_code ?? '' }}
                            </p>
                            @if($order->shipping_phone)
                                <small class="text-muted"><i class="bi bi-telephone-fill me-1"></i> {{ $order->shipping_phone }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- TRACKING INFO --}}
            @if($order->resi)
            <div class="mt-4 p-4 rounded-4 bg-emerald-soft border-emerald border-start border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-emerald fw-bold text-uppercase ls-1">Nomor Resi Pengiriman</small>
                        <h4 class="fw-bold m-0 text-dark">{{ $order->resi }}</h4>
                    </div>
                    <i class="bi bi-truck fs-1 text-emerald opacity-25"></i>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- RIGHT: SUMMARY & ACTIONS --}}
    <div class="col-lg-4">
        {{-- STATUS CARD --}}
        <div class="card card-modern border-0 p-4 mb-4">
            <h6 class="fw-bold mb-4 small text-muted text-uppercase ls-1">Status Pesanan</h6>
            <div class="text-center py-2">
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
                <span class="badge rounded-pill px-5 py-3 fw-bold text-uppercase ls-1 fs-6 {{ $statusClass }} w-100 mb-4">
                    {{ $order->status }}
                </span>
                
                <div class="d-grid gap-2">
                    <a href="{{ route('seller.orders.edit', $order->id) }}" class="btn btn-emerald rounded-4 py-3 fw-bold shadow-sm">
                        <i class="bi bi-pencil-square me-2"></i>Update Status & Resi
                    </a>
                    <a href="/seller/chat?user={{ $order->user_id }}" class="btn btn-light rounded-4 py-3 fw-bold text-muted border">
                        <i class="bi bi-chat-dots me-2"></i>Hubungi Pembeli
                    </a>
                </div>
            </div>
        </div>

        {{-- PAYMENT SUMMARY --}}
        <div class="card card-modern border-0 p-4 mb-4">
            <h6 class="fw-bold mb-4 small text-muted text-uppercase ls-1">Ringkasan Pembayaran</h6>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Subtotal Produk</span>
                <span class="fw-bold text-dark">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2 pb-3 border-bottom">
                <span class="text-muted">Biaya Pengiriman</span>
                <span class="fw-bold text-dark">Rp 0</span>
            </div>
            <div class="d-flex justify-content-between mt-3 mb-4">
                <h6 class="fw-bold m-0 text-dark">Total Tagihan</h6>
                <h5 class="fw-bold m-0 text-emerald">Rp {{ number_format($order->total, 0, ',', '.') }}</h5>
            </div>

            @if($order->bukti_pembayaran)
            <div class="bg-light rounded-4 p-3 text-center border dashed">
                <small class="text-muted d-block mb-2 fw-bold text-uppercase ls-1">Bukti Pembayaran</small>
                <a href="{{ asset($order->bukti_pembayaran) }}" target="_blank">
                    <img src="{{ asset($order->bukti_pembayaran) }}" class="img-fluid rounded-3 shadow-sm" style="max-height: 200px;">
                </a>
            </div>
            @else
            <div class="alert alert-warning small rounded-4 border-0 m-0">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Bukti pembayaran belum diunggah.
            </div>
            @endif
        </div>

        {{-- TIPS --}}
        <div class="card card-modern border-0 p-4 bg-dark text-white text-opacity-75">
            <h6 class="fw-bold text-white mb-2"><i class="bi bi-shield-check me-2 text-emerald"></i>Keamanan Seller</h6>
            <p class="small m-0">Pastikan untuk mendokumentasikan proses packing barang untuk menghindari kendala saat komplain.</p>
        </div>
    </div>
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .bg-emerald-soft { background-color: #ecfdf5; }
    .border-emerald { border: 1px solid #10B981 !important; }
    .bg-warning-subtle { background-color: #fffbeb; }
    .bg-info-subtle { background-color: #f0f9ff; }
    .bg-primary-subtle { background-color: #eff6ff; }
    .bg-danger-subtle { background-color: #fef2f2; }
    .object-fit-cover { object-fit: cover; }
    .dashed { border: 2px dashed #cbd5e1 !important; }
</style>
@endsection
