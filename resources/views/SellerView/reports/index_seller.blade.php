@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <h2 class="fw-bold m-0 text-dark">Laporan Bisnis Toko</h2>
    <p class="text-muted">Pantau perkembangan penjualan dan penyewaan alat camping Anda di sini.</p>
</div>

<div class="row g-4 mb-5">
    <!-- CARD LAPORAN PENJUALAN -->
    <div class="col-md-6">
        <div class="card card-modern h-100 border-0 overflow-hidden">
            <div class="card-body p-5">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="icon-box bg-emerald-soft text-emerald rounded-4 p-4 fs-1">
                        💰
                    </div>
                    <span class="badge bg-emerald-soft text-emerald rounded-pill px-3 py-2 fw-bold">PRODUK JUAL</span>
                </div>
                
                <h3 class="fw-bold mb-2">Laporan Penjualan</h3>
                <p class="text-muted mb-4">Ringkasan transaksi produk yang terjual secara permanen kepada pelanggan.</p>
                
                <div class="bg-light p-4 rounded-4 mb-5">
                    <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-1">Total Pendapatan</small>
                    <span class="h2 fw-bold text-dark">Rp {{ number_format($totalSales, 0, ',', '.') }}</span>
                </div>

                <div class="d-flex gap-3">
                    <a href="{{ route('seller.reports.sales') }}" class="btn btn-emerald flex-grow-1 py-3">
                        <i class="bi bi-eye me-2"></i>Buka Laporan
                    </a>
                    <a href="{{ route('seller.reports.exportPdf', 'sales') }}" target="_blank" class="btn btn-outline-dark rounded-4 px-4 py-3">
                        <i class="bi bi-download"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- CARD LAPORAN PENYEWAAN -->
    <div class="col-md-6">
        <div class="card card-modern h-100 border-0 overflow-hidden">
            <div class="card-body p-5">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="icon-box bg-primary-subtle text-primary rounded-4 p-4 fs-1">
                        🏕️
                    </div>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-bold">SEWA ALAT</span>
                </div>
                
                <h3 class="fw-bold mb-2">Laporan Penyewaan</h3>
                <p class="text-muted mb-4">Ringkasan transaksi penyewaan alat camping dan perlengkapan outdoor.</p>
                
                <div class="bg-light p-4 rounded-4 mb-5">
                    <div class="row g-3">
                        <div class="col-6">
                            <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-1" style="font-size: 0.65rem;">Pendapatan Bersih</small>
                            <span class="h4 fw-bold text-dark d-block">Rp {{ number_format($totalRentals, 0, ',', '.') }}</span>
                        </div>
                        <div class="col-6 border-start">
                            <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-1" style="font-size: 0.65rem;">Potongan Admin</small>
                            <span class="h4 fw-bold text-danger d-block">Rp {{ number_format($totalAdminFees, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3">
                    <a href="{{ route('seller.reports.rentals') }}" class="btn btn-primary flex-grow-1 py-3 rounded-4 fw-bold border-0" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                        <i class="bi bi-eye me-2"></i>Buka Laporan
                    </a>
                    <a href="{{ route('seller.reports.exportPdf', 'rentals') }}" target="_blank" class="btn btn-outline-dark rounded-4 px-4 py-3">
                        <i class="bi bi-download"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- HELP BOX --}}
<div class="card card-modern border-0 bg-white p-4">
    <div class="d-flex align-items-center gap-4">
        <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info fs-3">
            <i class="bi bi-info-circle-fill"></i>
        </div>
        <div>
            <h6 class="fw-bold mb-1">Butuh laporan dengan rentang tanggal khusus?</h6>
            <p class="text-muted small mb-0">Anda dapat memfilter data berdasarkan tanggal tertentu di dalam menu "Buka Laporan" sebelum mengunduh file PDF.</p>
        </div>
    </div>
</div>

<style>
    .ls-1 { letter-spacing: 1px; }
    .text-emerald { color: #10B981; }
    .bg-emerald-soft { background-color: #ecfdf5; }
    .fw-black { font-weight: 900; }
</style>
@endsection
