@extends('SellerView.layouts.app_seller')

@section('content')
<div class="d-flex" style="min-height:100vh; background:#f9fafb;">
    {{-- SIDEBAR --}}
    <div style="width:260px; background:#ffffff; border-right:1px solid #e5e7eb; display:flex; flex-direction:column; justify-content:space-between;">

        {{-- TOP --}}
        <div>

            {{-- BRAND --}}
            <div class="p-4 border-bottom">
                <h4 style="color:#10B981; font-weight:800; letter-spacing:1px;">CAMPIFY.</h4>
                <small class="text-muted">SELLER HUB</small>
            </div>

            {{-- MENU --}}
            <ul class="nav flex-column px-3 mt-3">

                {{-- DASHBOARD --}}
                <li class="nav-item mb-1">
                    <a class="nav-link sidebar-link {{ request()->routeIs('seller.dashboard') ? 'active' : '' }}"
                    href="{{ route('seller.dashboard') }}">
                        📊 Dashboard
                    </a>
                </li>

                {{-- PRODUK --}}
                <li class="nav-item mb-1">
                    <a class="nav-link sidebar-link {{ request()->routeIs('products*') ? 'active' : '' }}"
                    href="{{ route('seller.products.index') }}">
                        📦 Kelola Produk
                    </a>
                </li>

                {{-- RATING --}}
                <li class="nav-item mb-1">
                    <a class="nav-link sidebar-link {{ request()->routeIs('seller.ratings.index') ? 'active' : '' }}"
                    href="/seller/ratings">
                        ⭐ Kelola Rating
                    </a>
                </li>

                {{-- TRANSAKSI (DROPDOWN) --}}
                <li class="nav-item mb-1">

                    <a class="nav-link sidebar-link d-flex justify-content-between align-items-center"
                    data-bs-toggle="collapse"
                    href="#transaksiMenu"
                    role="button"
                    aria-expanded="false"
                    aria-controls="transaksiMenu">

                        💰 Transaksi
                        <span class="text-muted">▾</span>

                    </a>

                    <div class="collapse {{ request()->is('seller/orders*') || request()->is('seller/rentals*') ? 'show' : '' }}"
                        id="transaksiMenu">

                        <ul class="nav flex-column ms-3 mt-1">

                            <li class="nav-item">
                                <a class="nav-link sidebar-sub {{ request()->is('seller/orders*') ? 'active' : '' }}"
                                href="/seller/orders">
                                    🧾 Pesanan Baru
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link sidebar-sub {{ request()->is('seller/rentals*') ? 'active' : '' }}"
                                href="/seller/rentals">
                                    🏕️ Penyewaan Alat
                                </a>
                            </li>

                        </ul>

                    </div>
                </li>

                {{-- LAPORAN (DROPDOWN) --}}
                <li class="nav-item mb-1">

                    <a class="nav-link sidebar-link d-flex justify-content-between align-items-center"
                    data-bs-toggle="collapse"
                    href="#laporanMenu"
                    role="button"
                    aria-expanded="false"
                    aria-controls="laporanMenu">

                        📊 Laporan
                        <span class="text-muted">▾</span>

                    </a>

                    <div class="collapse {{ request()->is('seller/reports*') ? 'show' : '' }}"
                        id="laporanMenu">

                        <ul class="nav flex-column ms-3 mt-1">

                            <li class="nav-item">
                                <a class="nav-link sidebar-sub {{ request()->routeIs('seller.reports.sales') ? 'active' : '' }}"
                                href="{{ route('seller.reports.sales') }}">
                                    🛒 Laporan Penjualan
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link sidebar-sub {{ request()->routeIs('seller.reports.rentals') ? 'active' : '' }}"
                                href="{{ route('seller.reports.rentals') }}">
                                    🏕️ Laporan Penyewaan
                                </a>
                            </li>

                        </ul>

                    </div>
                </li>

                {{-- CHAT --}}
                <li class="nav-item mb-1">
                    <a class="nav-link sidebar-link {{ request()->routeIs('chat.index') ? 'active' : '' }}"
                    href="/seller/chat">
                        💬 Chat Pembeli
                    </a>
                </li>

            </ul>
        </div>

        {{-- BOTTOM --}}
        <div class="px-3 pb-4">
            <hr>
            <a class="nav-link sidebar-link {{ request()->routeIs('seller.store-profile*') ? 'bg-success text-white rounded px-3 py-2' : 'text-dark' }}" href="{{ route('seller.store-profile.index') }}"">
                👤 Profil Toko
            </a>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="flex-grow-1 p-4" style="background:#f9fafb; min-height:100vh;">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h3 class="fw-bold mb-1">Laporan Penyewaan</h3>
                <p class="text-muted mb-0">Ringkasan penyewaan produk Anda</p>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-light rounded-pill px-3" onclick="window.print()">
                    🖨️ Cetak Laporan
                </button>
                <button class="btn btn-success rounded-pill px-3" onclick="exportPDF()">
                    📄 Export PDF
                </button>
            </div>
        </div>

        {{-- FILTER --}}
        <div class="card border-0 shadow-sm p-3 mb-4" style="border-radius:16px;">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3" style="border-radius:16px;">
                    <small class="text-muted">Total Pendapatan Sewa</small>
                    <h4 class="fw-bold mt-1">Rp {{ number_format($totalRentalIncome, 0, ',', '.') }}</h4>
                    <small class="text-success">{{ $totalRentals }} penyewaan</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3" style="border-radius:16px;">
                    <small class="text-muted">Rata-rata per Penyewaan</small>
                    <h4 class="fw-bold mt-1">Rp {{ $totalRentals > 0 ? number_format($totalRentalIncome / $totalRentals, 0, ',', '.') : '0' }}</h4>
                    <small class="text-primary">Per transaksi</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3" style="border-radius:16px;">
                    <small class="text-muted">Produk Tersewa</small>
                    <h4 class="fw-bold mt-1">{{ $topRentedProducts->first()['nama_produk'] ?? '-' }}</h4>
                    <small class="text-warning">{{ $topRentedProducts->first()['count'] ?? 0 }} kali</small>
                </div>
            </div>
        </div>

        <div class="row g-4">

            {{-- LEFT --}}
            <div class="col-md-8">

                <h5 class="fw-bold mb-3">Detail Penyewaan</h5>

                @foreach($rentals as $rental)
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius:14px;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <strong>{{ $rental->user->name ?? 'User' }}</strong><br>
                            <small class="text-muted">{{ $rental->product->nama_produk ?? '-' }}</small><br>
                            <small class="text-muted">{{ $rental->tanggal_mulai->format('d M Y') }} - {{ $rental->tanggal_selesai->format('d M Y') }}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">Rp {{ number_format($rental->total_harga, 0, ',', '.') }}</div>
                            <small class="badge bg-success">Selesai</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Durasi: {{ $rental->duration }} hari</small>
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-muted">Harga/hari: Rp {{ number_format($rental->price, 0, ',', '.') }}</small>
                        </div>
                    </div>
                </div>
                @endforeach

                @if($rentals->isEmpty())
                <div class="text-center py-5">
                    <p class="text-muted">Belum ada penyewaan di periode ini</p>
                </div>
                @endif

            </div>

            {{-- RIGHT --}}
            <div class="col-md-4">

                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius:16px;">
                    <h6 class="fw-bold mb-3">Produk Tersewa Terbanyak</h6>

                    @foreach($topRentedProducts as $product)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small">{{ Str::limit($product['nama_produk'], 20) }}</span>
                        <span class="badge bg-primary">{{ $product['count'] }}x</span>
                    </div>
                    @endforeach

                    @if($topRentedProducts->isEmpty())
                    <p class="text-muted small">Belum ada data</p>
                    @endif
                </div>

            </div>

        </div>

    </div>
</div>

{{-- TAMPILAN CETAK FORMAL (HIDDEN DI LAYAR) --}}
<div class="print-only" style="display: none;">
    {{-- HEADER LAPORAN (untuk cetak) --}}
    <div class="report-header">
        <div class="company-name">CAMPIFY MARKETPLACE</div>
        <h1>LAPORAN PENYEWAAN PRODUK</h1>
        <div class="period">
            Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
        </div>
        <div>Dicetak pada: {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}</div>
    </div>

    {{-- RINGKASAN --}}
    <div class="summary-section">
        <div class="summary-row">
            <div class="summary-cell">
                <div class="summary-value">Rp {{ number_format($totalRentalIncome, 0, ',', '.') }}</div>
                <div class="summary-label">Total Pendapatan Sewa</div>
            </div>
            <div class="summary-cell">
                <div class="summary-value">{{ $totalRentals }}</div>
                <div class="summary-label">Jumlah Penyewaan</div>
            </div>
            <div class="summary-cell">
                <div class="summary-value">Rp {{ $totalRentals > 0 ? number_format($totalRentalIncome / $totalRentals, 0, ',', '.') : '0' }}</div>
                <div class="summary-label">Rata-rata per Penyewaan</div>
            </div>
            <div class="summary-cell">
                <div class="summary-value">{{ $topRentedProducts->first()['nama_produk'] ?? '-' }}</div>
                <div class="summary-label">Produk Tersewa</div>
            </div>
        </div>
    </div>

    {{-- DETAIL PENYEWAAN --}}
    <div class="section-header">Detail Penyewaan</div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="20%">Penyewa</th>
                <th width="25%">Produk</th>
                <th width="10%">Durasi</th>
                <th width="10%">Qty</th>
                <th width="15%">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($rentals as $rental)
            <tr>
                <td class="number">{{ $no++ }}</td>
                <td>{{ $rental->created_at->format('d/m/Y') }}</td>
                <td>{{ $rental->user->name ?? 'N/A' }}</td>
                <td>{{ $rental->product->nama_produk ?? '-' }}</td>
                <td class="number">{{ $rental->duration }} hari</td>
                <td class="number">Rp {{ number_format($rental->price, 0, ',', '.') }}</td>
                <td class="number">Rp {{ number_format($rental->total_harga, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            @if($rentals->isEmpty())
            <tr>
                <td colspan="7" style="text-align: center;">Tidak ada data penyewaan dalam periode ini</td>
            </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" style="text-align: right;">TOTAL</th>
                <th class="number">Rp {{ number_format($totalRentalIncome, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    {{-- PRODUK TERSEWA TERBANYAK --}}
    @if($topRentedProducts->isNotEmpty())
    <div class="section-header">Produk Tersewa Terbanyak</div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="50%">Nama Produk</th>
                <th width="15%">Jumlah Sewa</th>
                <th width="30%">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topRentedProducts->take(10) as $index => $product)
            <tr>
                <td class="number">{{ $index + 1 }}</td>
                <td>{{ $product['nama_produk'] }}</td>
                <td class="number">{{ $product['count'] }}</td>
                <td class="number">Rp {{ number_format($product['total'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- FOOTER --}}
    <div class="report-footer">
        <div>Laporan ini dicetak secara otomatis oleh sistem Campify</div>
        <div class="signature">
            <div>Mengetahui,</div>
            <div class="signature-line"></div>
            <div>Admin Campify</div>
        </div>
    </div>
</div>

<script>
function exportPDF() {
    // Simple print for now, can be enhanced with PDF library later
    window.print();
}
</script>

<style>
/* Print-only styling */
@media print {
    @page {
        size: A4;
        margin: 1.5cm;
    }

    body {
        font-family: 'Times New Roman', serif;
        font-size: 12px;
        line-height: 1.4;
        color: #000;
        background: white !important;
    }

    /* Hide UI elements, show print-only content */
    .sidebar, .btn, form, .d-flex.justify-content-between.align-items-start.mb-4 .d-flex.gap-2,
    .navbar, .footer, .alert, .main-content {
        display: none !important;
    }

    .print-only {
        display: block !important;
    }

    /* Header Laporan */
    .report-header {
        text-align: center;
        border-bottom: 2px solid #000;
        padding-bottom: 20px;
        margin-bottom: 30px;
    }

    .report-header h1 {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 5px;
        text-transform: uppercase;
    }

    .report-header .company-name {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .report-header .period {
        font-size: 12px;
        margin-bottom: 5px;
    }

    /* Summary Section */
    .summary-section {
        display: table;
        width: 100%;
        margin-bottom: 30px;
        border-collapse: collapse;
    }

    .summary-row {
        display: table-row;
    }

    .summary-cell {
        display: table-cell;
        width: 25%;
        padding: 10px;
        border: 1px solid #ddd;
        text-align: center;
        vertical-align: top;
    }

    .summary-value {
        font-size: 14px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .summary-label {
        font-size: 11px;
        color: #666;
    }

    /* Section Headers */
    .section-header {
        font-size: 14px;
        font-weight: bold;
        margin: 30px 0 15px 0;
        text-transform: uppercase;
        border-bottom: 1px solid #000;
        padding-bottom: 5px;
    }

    /* Data Tables */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        font-size: 11px;
    }

    .data-table th {
        background-color: #f5f5f5;
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
        font-weight: bold;
        font-size: 11px;
    }

    .data-table td {
        border: 1px solid #ddd;
        padding: 6px;
        vertical-align: top;
    }

    .data-table .number {
        text-align: right;
    }

    .data-table tfoot th {
        background-color: #e9ecef;
        font-weight: bold;
    }

    /* Report Footer */
    .report-footer {
        margin-top: 50px;
        text-align: right;
        font-size: 11px;
    }

    .signature {
        margin-top: 40px;
        text-align: center;
    }

    .signature-line {
        border-bottom: 1px solid #000;
        width: 150px;
        margin: 40px auto 5px auto;
    }
}
</style>
        margin-bottom: 5px;
    }

    .summary-label {
        font-size: 10px;
        color: #666;
    }

    /* Tabel Data */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
        font-size: 11px;
    }

    .data-table th,
    .data-table td {
        border: 1px solid #000;
        padding: 8px;
        text-align: left;
        vertical-align: top;
    }

    .data-table th {
        background-color: #f5f5f5;
        font-weight: bold;
        text-align: center;
    }

    .data-table .number {
        text-align: right;
    }

    /* Section Headers */
    .section-header {
        font-size: 14px;
        font-weight: bold;
        margin: 30px 0 15px 0;
        padding-bottom: 5px;
        border-bottom: 1px solid #000;
        text-transform: uppercase;
    }

    /* Footer */
    .report-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        border-top: 1px solid #000;
        padding-top: 10px;
        font-size: 10px;
        text-align: center;
    }

    .report-footer .signature {
        margin-top: 40px;
        text-align: center;
    }

    .report-footer .signature-line {
        border-bottom: 1px solid #000;
        width: 200px;
        margin: 0 auto;
        margin-top: 20px;
    }

    /* Page breaks */
    .page-break {
        page-break-before: always;
    }

    /* Hide elements not needed in print */
    .no-print {
        display: none !important;
    }
</style>

<div class="no-print">
    {{-- Tombol cetak tetap ditampilkan di layar --}}
</div>

{{-- HEADER LAPORAN (untuk cetak) --}}
<div class="report-header">
    <div class="company-name">CAMPIFY MARKETPLACE</div>
    <h1>LAPORAN PENYEWAAN PRODUK</h1>
    <div class="period">
        Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
    </div>
    <div>Dicetak pada: {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}</div>
</div>

{{-- RINGKASAN --}}
<div class="summary-section">
    <div class="summary-row">
        <div class="summary-cell">
            <div class="summary-value">Rp {{ number_format($totalRentalIncome, 0, ',', '.') }}</div>
            <div class="summary-label">Total Pendapatan Sewa</div>
        </div>
        <div class="summary-cell">
            <div class="summary-value">{{ $totalRentals }}</div>
            <div class="summary-label">Jumlah Penyewaan</div>
        </div>
        <div class="summary-cell">
            <div class="summary-value">Rp {{ $totalRentals > 0 ? number_format($totalRentalIncome / $totalRentals, 0, ',', '.') : '0' }}</div>
            <div class="summary-label">Rata-rata per Penyewaan</div>
        </div>
        <div class="summary-cell">
            <div class="summary-value">{{ $topRentedProducts->first()['nama_produk'] ?? '-' }}</div>
            <div class="summary-label">Produk Tersewa</div>
        </div>
    </div>
</div>

{{-- DETAIL PENYEWAAN --}}
<div class="section-header">Detail Penyewaan</div>

<table class="data-table">
    <thead>
        <tr>
            <th width="5%">No</th>
            <th width="15%">Tanggal</th>
            <th width="20%">Penyewa</th>
            <th width="25%">Produk</th>
            <th width="10%">Durasi</th>
            <th width="10%">Qty</th>
            <th width="15%">Total</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach($rentals as $rental)
        <tr>
            <td class="number">{{ $no++ }}</td>
            <td>{{ $rental->created_at->format('d/m/Y') }}</td>
            <td>{{ $rental->user->name ?? 'N/A' }}</td>
            <td>{{ $rental->product->nama_produk ?? '-' }}</td>
            <td class="number">{{ $rental->duration ?? 1 }} hari</td>
            <td class="number">1</td>
            <td class="number">Rp {{ number_format($rental->price ?? $rental->total_harga, 0, ',', '.') }}</td>
        </tr>
        @endforeach
        @if($rentals->isEmpty())
        <tr>
            <td colspan="7" style="text-align: center;">Tidak ada data penyewaan dalam periode ini</td>
        </tr>
        @endif
    </tbody>
    <tfoot>
        <tr>
            <th colspan="6" style="text-align: right;">TOTAL</th>
            <th class="number">Rp {{ number_format($totalRentalIncome, 0, ',', '.') }}</th>
        </tr>
    </tfoot>
</table>

{{-- PRODUK TERSEWA TERBANYAK --}}
@if($topRentedProducts->isNotEmpty())
<div class="section-header">Produk Tersewa Terbanyak</div>

<table class="data-table">
    <thead>
        <tr>
            <th width="5%">No</th>
            <th width="50%">Nama Produk</th>
            <th width="15%">Jumlah Sewa</th>
            <th width="30%">Total Pendapatan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($topRentedProducts->take(10) as $index => $product)
        <tr>
            <td class="number">{{ $index + 1 }}</td>
            <td>{{ $product['nama_produk'] }}</td>
            <td class="number">{{ $product['count'] }}</td>
            <td class="number">Rp {{ number_format($product['total'], 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- FOOTER --}}
<div class="report-footer">
    <div>Laporan ini dicetak secara otomatis oleh sistem Campify</div>
    <div class="signature">
        <div>Mengetahui,</div>
        <div class="signature-line"></div>
        <div>Admin Campify</div>
    </div>
</div>