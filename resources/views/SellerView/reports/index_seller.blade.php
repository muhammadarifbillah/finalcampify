@extends('SellerView.layouts.app_seller')

@section('content')
<div class="pt-4 pb-5">
    <div class="mb-5">
        <h2 class="fw-bold text-dark">Laporan Bisnis Toko</h2>
        <p class="text-muted">Pantau perkembangan penjualan dan penyewaan alat camping Anda di sini.</p>
    </div>

    <div class="row g-4">
        <!-- CARD LAPORAN PENJUALAN -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 24px; transition: 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="bg-success bg-opacity-10 p-3 rounded-4">
                            <span style="font-size: 2rem;">💰</span>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Produk Jual</span>
                    </div>
                    <h3 class="fw-bold mb-1">Laporan Penjualan</h3>
                    <p class="text-muted small mb-4">Ringkasan transaksi produk yang terjual secara permanen.</p>
                    
                    <div class="bg-light p-3 rounded-4 mb-4">
                        <small class="text-muted d-block">Total Pendapatan</small>
                        <span class="fs-4 fw-black text-dark">Rp {{ number_format($totalSales) }}</span>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('seller.reports.sales') }}" class="btn btn-success rounded-pill py-2 fw-bold">
                            Buka Laporan
                        </a>
                        <a href="{{ route('seller.reports.exportPdf', 'sales') }}" target="_blank" class="btn btn-outline-success rounded-pill py-2 fw-bold">
                            📥 Unduh PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD LAPORAN PENYEWAAN -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 24px; transition: 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-4">
                            <span style="font-size: 2rem;">🏕️</span>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">Sewa Alat</span>
                    </div>
                    <h3 class="fw-bold mb-1">Laporan Penyewaan</h3>
                    <p class="text-muted small mb-4">Ringkasan transaksi penyewaan alat camping dan perlengkapan.</p>
                    
                    <div class="bg-light p-3 rounded-4 mb-4">
                        <small class="text-muted d-block">Total Pendapatan Sewa</small>
                        <span class="fs-4 fw-black text-dark">Rp {{ number_format($totalRentals) }}</span>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('seller.reports.rentals') }}" class="btn btn-primary rounded-pill py-2 fw-bold" style="background-color: #0d6efd; border-color: #0d6efd;">
                            Buka Laporan
                        </a>
                        <a href="{{ route('seller.reports.exportPdf', 'rentals') }}" target="_blank" class="btn btn-outline-primary rounded-pill py-2 fw-bold">
                            📥 Unduh PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- INFO BOX -->
    <div class="mt-5 p-4 bg-white rounded-4 border border-info border-opacity-25 shadow-sm">
        <div class="d-flex gap-3 align-items-center">
            <span style="font-size: 1.5rem;">ℹ️</span>
            <div>
                <h6 class="fw-bold mb-1">Butuh laporan khusus?</h6>
                <p class="text-muted small mb-0">Klik "Buka Laporan" untuk memfilter data berdasarkan rentang tanggal tertentu sebelum mengunduh PDF.</p>
            </div>
        </div>
    </div>
</div>
@endsection
