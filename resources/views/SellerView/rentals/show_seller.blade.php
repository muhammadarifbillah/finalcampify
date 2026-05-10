@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="d-flex align-items-center">
        <a href="{{ route('seller.rentals.index') }}" class="btn btn-light rounded-circle p-3 me-4 shadow-sm border-0 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="bi bi-arrow-left fs-4"></i>
        </a>
        <div>
            <h2 class="fw-bold m-0 text-dark">Detail Penyewaan</h2>
            <p class="text-muted">Informasi lengkap transaksi sewa unit #{{ $rental->id }}</p>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- LEFT: RENTAL DETAILS --}}
    <div class="col-lg-8">
        {{-- USER & SHIPPING INFO --}}
        <div class="card card-modern border-0 p-5 mb-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-5 pb-3 border-bottom">
                <h5 class="fw-bold m-0 text-dark"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Data Penyewa & Pengiriman</h5>
                @php
                    $statusClass = match($rental->status) {
                        'pending' => 'bg-warning-subtle text-warning',
                        'active' => 'bg-info-subtle text-info',
                        'completed' => 'bg-emerald-soft text-emerald',
                        'cancelled' => 'bg-danger-subtle text-danger',
                        default => 'bg-light text-dark'
                    };
                @endphp
                <span class="badge rounded-pill px-4 py-2 fw-bold text-uppercase ls-1 {{ $statusClass }}">
                    {{ $rental->status }}
                </span>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex gap-3">
                        <div class="p-3 bg-light rounded-circle text-primary fs-4" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-1" style="font-size: 0.7rem;">Nama Penyewa</small>
                            <h5 class="fw-bold m-0 text-dark">{{ $rental->user->name ?? 'User' }}</h5>
                            <p class="text-muted small m-0">{{ $rental->user->email ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 border-start-md">
                    <div class="d-flex gap-3">
                        <div class="p-3 bg-light rounded-circle text-primary fs-4" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div class="flex-grow-1">
                            <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-1" style="font-size: 0.7rem;">Alamat Pengiriman</small>
                            <p class="text-dark small leading-relaxed m-0 fw-semibold">
                                @if($rental->order)
                                    {{ $rental->order->shipping_address ?? 'Alamat tidak tersedia' }}<br>
                                    <span class="text-muted">{{ $rental->order->shipping_district ?? '' }}, {{ $rental->order->shipping_city ?? '' }} {{ $rental->order->shipping_postal_code ?? '' }}</span>
                                @else
                                    <span class="text-muted italic">Data alamat tidak tertaut ke pesanan.</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PRODUCT INFO --}}
        <div class="card card-modern border-0 p-5 mb-4 shadow-sm">
            <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-box-seam me-2 text-primary"></i>Informasi Produk</h5>
            <div class="row g-4 align-items-center">
                <div class="col-md-5">
                    <div class="rounded-4 overflow-hidden border shadow-sm" style="height: 280px;">
                        @if($rental->product->gambar && file_exists(public_path('storage/'.$rental->product->gambar)))
                            <img src="{{ asset('storage/'.$rental->product->gambar) }}" class="w-100 h-100 object-fit-cover">
                        @else
                            <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center opacity-25 fs-1">🏕️</div>
                        @endif
                    </div>
                </div>
                <div class="col-md-7">
                    <small class="text-primary fw-bold text-uppercase ls-1" style="font-size: 0.7rem;">{{ $rental->product->kategori ?? 'Camping Gear' }}</small>
                    <h3 class="fw-bold text-dark mb-2">{{ $rental->product->nama_produk ?? '-' }}</h3>
                    <p class="text-muted leading-relaxed mb-4">{{ Str::limit($rental->product->deskripsi, 180) }}</p>
                    
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-4 text-center">
                                <small class="text-muted d-block mb-1 small text-uppercase">Mulai Sewa</small>
                                <span class="fw-bold text-dark">{{ optional($rental->start_date)->format('d M Y') }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-4 text-center">
                                <small class="text-muted d-block mb-1 small text-uppercase">Kembali</small>
                                <span class="fw-bold text-dark">{{ optional($rental->end_date)->format('d M Y') }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-primary bg-opacity-10 rounded-4 text-center border border-primary border-opacity-10">
                                <span class="text-primary fw-bold fs-5"><i class="bi bi-calendar-check me-2"></i>Durasi: {{ $rental->duration }} Hari</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: SUMMARY & ACTIONS --}}
    <div class="col-lg-4">
        {{-- PAYMENT SUMMARY --}}
        <div class="card card-modern border-0 p-4 mb-4 shadow-sm">
            <h6 class="fw-bold mb-4 small text-muted text-uppercase ls-1">Ringkasan Pembayaran</h6>
            
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted small">Harga Sewa / Hari</span>
                <span class="fw-bold text-dark">Rp {{ number_format($rental->price, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between mb-4 pb-3 border-bottom">
                <span class="text-muted small">Total Durasi</span>
                <span class="fw-bold text-dark">{{ $rental->duration }} Hari</span>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-5">
                <span class="h6 fw-bold m-0 text-dark">Total Biaya</span>
                <span class="h4 fw-bold m-0 text-primary">Rp {{ number_format($rental->price * $rental->duration, 0, ',', '.') }}</span>
            </div>

            <div class="d-grid gap-3">
                <a href="{{ route('seller.rentals.edit', $rental->id) }}" class="btn btn-primary rounded-4 py-3 fw-bold border-0 shadow-sm" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                    <i class="bi bi-pencil-square me-2"></i>Update Status Sewa
                </a>
                <a href="/seller/chat?user={{ $rental->user_id }}" class="btn btn-light rounded-4 py-3 fw-bold text-muted border">
                    <i class="bi bi-chat-dots me-2"></i>Hubungi Penyewa
                </a>
            </div>
        </div>

        {{-- BUKTI PEMBAYARAN --}}
        @if($rental->bukti_pembayaran)
        <div class="card card-modern border-0 p-4 mb-4 shadow-sm">
            <h6 class="fw-bold mb-3 small text-muted text-uppercase ls-1">Bukti Pembayaran</h6>
            <div class="bg-light rounded-4 overflow-hidden border dashed p-2 text-center">
                <a href="{{ asset($rental->bukti_pembayaran) }}" target="_blank">
                    <img src="{{ asset($rental->bukti_pembayaran) }}" class="img-fluid rounded-3 shadow-sm" style="max-height: 250px;">
                </a>
            </div>
        </div>
        @endif

        <div class="card card-modern border-0 p-4 bg-info bg-opacity-10 text-info border-start border-info border-4">
            <h6 class="fw-bold mb-2"><i class="bi bi-info-circle-fill me-2"></i>Informasi Penting</h6>
            <p class="small mb-0 leading-relaxed text-dark opacity-75">Pastikan Anda telah memeriksa kondisi barang secara mendetail sebelum menyerahkan atau menerima kembali barang dari penyewa.</p>
        </div>
    </div>
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .bg-emerald-soft { background-color: #ecfdf5; }
    .bg-warning-subtle { background-color: #fffbeb; }
    .bg-info-subtle { background-color: #f0f9ff; }
    .object-fit-cover { object-fit: cover; }
    .leading-relaxed { line-height: 1.6; }
    .dashed { border: 2px dashed #cbd5e1 !important; }
    
    @media (min-width: 768px) {
        .border-start-md { border-left: 1px solid #eef2f7 !important; padding-left: 2rem; }
    }
</style>
@endsection
