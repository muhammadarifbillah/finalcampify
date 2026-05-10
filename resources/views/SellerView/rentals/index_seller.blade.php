@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold m-0 text-dark">Penyewaan Alat</h2>
            <p class="text-muted">Kelola transaksi sewa perlengkapan camping pelanggan Anda.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-4 py-2 fw-bold border border-primary border-opacity-10">
                {{ $rentals->count() }} Total Transaksi Sewa
            </div>
        </div>
    </div>
</div>

{{-- STATUS TABS --}}
<div class="card card-modern p-3 mb-5 border-0">
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('seller.rentals.index') }}" 
           class="btn rounded-pill px-4 py-2 fw-semibold {{ request('status') == null ? 'btn-primary border-0' : 'btn-light text-muted' }}" 
           style="{{ request('status') == null ? 'background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);' : '' }}">
           Semua
        </a>
        <a href="{{ route('seller.rentals.index', ['status' => 'pending']) }}" 
           class="btn rounded-pill px-4 py-2 fw-semibold {{ request('status') == 'pending' ? 'btn-warning text-dark' : 'btn-light text-muted' }}">
           Menunggu Konfirmasi
        </a>
        <a href="{{ route('seller.rentals.index', ['status' => 'active']) }}" 
           class="btn rounded-pill px-4 py-2 fw-semibold {{ request('status') == 'active' ? 'btn-info text-white' : 'btn-light text-muted' }}">
           Sedang Disewa
        </a>
        <a href="{{ route('seller.rentals.index', ['status' => 'completed']) }}" 
           class="btn rounded-pill px-4 py-2 fw-semibold {{ request('status') == 'completed' ? 'btn-emerald' : 'btn-light text-muted' }}">
           Selesai
        </a>
    </div>
</div>

{{-- RENTALS LIST --}}
<div class="rentals-container">
    @forelse($rentals as $rental)
    <div class="card card-modern border-0 mb-4 overflow-hidden shadow-sm">
        <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-box bg-primary bg-opacity-10 rounded-3 p-3 text-primary">
                    <i class="bi bi-calendar-check fs-4"></i>
                </div>
                <div>
                    <h6 class="fw-bold m-0">Sewa #{{ $rental->id }}</h6>
                    <small class="text-muted">{{ $rental->created_at->format('d M Y, H:i') }} • {{ $rental->user->name ?? 'User' }}</small>
                    @if($rental->user->ktp_verified_at)
                        <span class="badge bg-primary bg-opacity-10 text-primary ms-2" style="font-size: 8px;">VERIFIED KTP</span>
                    @else
                        <span class="badge bg-danger bg-opacity-10 text-danger ms-2 animate-pulse" style="font-size: 8px;">KTP UNVERIFIED</span>
                    @endif
                </div>
            </div>
            <div>
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
        </div>
        
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-4 p-3 bg-light rounded-4 h-100">
                        @if($rental->product->image)
                            <img src="{{ asset($rental->product->image) }}" class="rounded-3 shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="bg-white rounded-3 d-flex align-items-center justify-content-center border" style="width: 100px; height: 100px;">🏕️</div>
                        @endif
                        <div class="flex-grow-1">
                            <h5 class="fw-bold m-0 text-dark">{{ $rental->product->nama_produk ?? '-' }}</h5>
                            <p class="text-muted small mb-2"><i class="bi bi-tag me-1"></i> Rp {{ number_format($rental->price, 0, ',', '.') }} / hari</p>
                            <div class="d-flex gap-4">
                                <div>
                                    <small class="text-muted d-block">Durasi</small>
                                    <span class="fw-bold">{{ $rental->duration }} Hari</span>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Mulai - Selesai</small>
                                    <span class="fw-bold small">{{ \Carbon\Carbon::parse($rental->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($rental->end_date)->format('d M Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="p-4 rounded-4 h-100 d-flex flex-column justify-content-between" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                        <div>
                            <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-1">Total Biaya Sewa</small>
                            <h4 class="fw-bold text-primary mb-1">Rp {{ number_format($rental->price, 0, ',', '.') }}</h4>
                            <div class="text-muted small mb-3">Escrow: Rp {{ number_format($rental->product->buy_price * 0.5, 0, ',', '.') }}</div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="{{ route('seller.rentals.show', $rental->id) }}" class="btn btn-primary rounded-3 py-2 fw-bold border-0 shadow-sm" style="background: #3b82f6;">
                                <i class="bi bi-eye me-2"></i>Lihat Detail
                            </a>
                            <a href="{{ route('seller.rentals.edit', $rental->id) }}" class="btn btn-light rounded-3 py-2 fw-bold text-muted border">
                                <i class="bi bi-pencil-square me-2"></i>Update Status
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="card card-modern p-5 text-center border-0 bg-white shadow-sm rounded-4">
        <div class="mb-4 fs-1 opacity-25">🏕️</div>
        <h4 class="fw-bold">Belum Ada Transaksi Sewa</h4>
        <p class="text-muted">Semua pengajuan sewa perlengkapan akan muncul di sini.</p>
    </div>
    @endforelse
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .bg-emerald-soft { background-color: #ecfdf5; }
    .bg-warning-subtle { background-color: #fffbeb; }
    .bg-info-subtle { background-color: #f0f9ff; }
    .bg-danger-subtle { background-color: #fef2f2; }
    .animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .5; } }
</style>
@endsection
