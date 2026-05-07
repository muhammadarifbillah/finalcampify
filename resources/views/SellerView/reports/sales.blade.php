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
                <h3 class="fw-bold mb-1">Laporan Penjualan</h3>
                <p class="text-muted mb-0">Ringkasan penjualan produk Anda</p>
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
                    <small class="text-muted">Total Penjualan</small>
                    <h4 class="fw-bold mt-1">Rp {{ number_format($totalSales, 0, ',', '.') }}</h4>
                    <small class="text-success">{{ $totalOrders }} pesanan</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3" style="border-radius:16px;">
                    <small class="text-muted">Rata-rata per Pesanan</small>
                    <h4 class="fw-bold mt-1">Rp {{ $totalOrders > 0 ? number_format($totalSales / $totalOrders, 0, ',', '.') : '0' }}</h4>
                    <small class="text-primary">Per transaksi</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3" style="border-radius:16px;">
                    <small class="text-muted">Produk Terlaris</small>
                    <h4 class="fw-bold mt-1">{{ $topProducts->first()['nama_produk'] ?? '-' }}</h4>
                    <small class="text-warning">{{ $topProducts->first()['quantity'] ?? 0 }} terjual</small>
                </div>
            </div>
        </div>

        <div class="row g-4">

            {{-- LEFT --}}
            <div class="col-md-8">

                <h5 class="fw-bold mb-3">Detail Penjualan</h5>

                {{-- CARD LAPORAN FORMAL --}}
                <div class="card border-0 shadow-sm mt-4" style="border-radius:16px;">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold">Laporan Formal (Preview Cetak)</h5>
                            <small class="text-muted">Tampilan ini akan digunakan saat print / export PDF</small>
                        </div>
                        <button class="btn btn-outline-dark btn-sm" onclick="window.print()">
                            🖨️ Cetak
                        </button>
                    </div>

                    <div class="card-body">

                        {{-- HEADER LAPORAN --}}
                        <div class="text-center border-bottom pb-3 mb-3">
                            <h6 class="fw-bold mb-1">CAMPIFY MARKETPLACE</h6>
                            <h5 class="fw-bold text-uppercase">LAPORAN PENJUALAN PRODUK</h5>
                            <small>
                                Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
                                - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                            </small>
                        </div>

                        {{-- SUMMARY --}}
                        <div class="row text-center mb-3">
                            <div class="col">
                                <small>Total Penjualan</small>
                                <h6>Rp {{ number_format($totalSales, 0, ',', '.') }}</h6>
                            </div>
                            <div class="col">
                                <small>Jumlah Pesanan</small>
                                <h6>{{ $totalOrders }}</h6>
                            </div>
                            <div class="col">
                                <small>Rata-rata</small>
                                <h6>
                                    Rp {{ $totalOrders > 0 ? number_format($totalSales / $totalOrders, 0, ',', '.') : 0 }}
                                </h6>
                            </div>
                        </div>

                        {{-- TABEL SINGKAT --}}
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Pembeli</th>
                                        <th>Produk</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                        <td>{{ $order->buyer->name ?? '-' }}</td>
                                        <td>
                                            @foreach($order->details as $detail)
                                                {{ $detail->product->nama_produk ?? '-' }} ({{ $detail->qty }}x)<br>
                                            @endforeach
                                        </td>
                                        <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-end">TOTAL</th>
                                        <th>Rp {{ number_format($totalSales, 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>

                @foreach($orders as $order)
                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius:14px;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <strong>{{ $order->buyer->name ?? 'User' }}</strong><br>
                            <small class="text-muted">{{ $order->created_at->format('d M Y H:i') }}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                            <small class="badge bg-success">Selesai</small>
                        </div>
                    </div>

                    @foreach($order->details as $detail)
                    <div class="d-flex justify-content-between align-items-center py-1">
                        <span>{{ $detail->product->nama_produk ?? '-' }} ({{ $detail->qty }}x)</span>
                        <span>Rp {{ number_format($detail->harga * $detail->qty, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                @endforeach

                @if($orders->isEmpty())
                <div class="text-center py-5">
                    <p class="text-muted">Belum ada penjualan di periode ini</p>
                </div>
                @endif

            </div>

            {{-- RIGHT --}}
            <div class="col-md-4">

                <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius:16px;">
                    <h6 class="fw-bold mb-3">Produk Terlaris</h6>

                    @foreach($topProducts as $product)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small">{{ Str::limit($product['nama_produk'], 20) }}</span>
                        <span class="badge bg-primary">{{ $product['quantity'] }}x</span>
                    </div>
                    @endforeach

                    @if($topProducts->isEmpty())
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
        <h1>LAPORAN PENJUALAN PRODUK</h1>
        <div class="period">
            Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
        </div>
        <div>Dicetak pada: {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}</div>
    </div>

    {{-- RINGKASAN --}}
    <div class="summary-section">
        <div class="summary-row">
            <div class="summary-cell">
                <div class="summary-value">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
                <div class="summary-label">Total Penjualan</div>
            </div>
            <div class="summary-cell">
                <div class="summary-value">{{ $totalOrders }}</div>
                <div class="summary-label">Jumlah Pesanan</div>
            </div>
            <div class="summary-cell">
                <div class="summary-value">Rp {{ $totalOrders > 0 ? number_format($totalSales / $totalOrders, 0, ',', '.') : '0' }}</div>
                <div class="summary-label">Rata-rata per Pesanan</div>
            </div>
            <div class="summary-cell">
                <div class="summary-value">{{ $topProducts->first()['nama_produk'] ?? '-' }}</div>
                <div class="summary-label">Produk Terlaris</div>
            </div>
        </div>
    </div>

    {{-- DETAIL PENJUALAN --}}
    <div class="section-header">Detail Penjualan</div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="20%">Pembeli</th>
                <th width="35%">Produk</th>
                <th width="10%">Qty</th>
                <th width="15%">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($orders as $order)
            <tr>
                <td class="number">{{ $no++ }}</td>
                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                <td>{{ $order->buyer->name ?? 'N/A' }}</td>
                <td>
                    @foreach($order->details as $detail)
                    {{ $detail->product->nama_produk ?? '-' }} ({{ $detail->qty }}x)<br>
                    @endforeach
                </td>
                <td class="number">
                    @php
                    $totalQty = $order->details->sum('qty');
                    @endphp
                    {{ $totalQty }}
                </td>
                <td class="number">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            @if($orders->isEmpty())
            <tr>
                <td colspan="6" style="text-align: center;">Tidak ada data penjualan dalam periode ini</td>
            </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" style="text-align: right;">TOTAL</th>
                <th class="number">Rp {{ number_format($totalSales, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    {{-- PRODUK TERLARIS --}}
    @if($topProducts->isNotEmpty())
    <div class="section-header">Produk Terlaris</div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="60%">Nama Produk</th>
                <th width="15%">Terjual</th>
                <th width="20%">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topProducts->take(10) as $index => $product)
            <tr>
                <td class="number">{{ $index + 1 }}</td>
                <td>{{ $product['nama_produk'] }}</td>
                <td class="number">{{ $product['quantity'] }}</td>
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

<style media="print">
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

    .sidebar, .btn, form, .d-flex.justify-content-between.align-items-start.mb-4 .d-flex.gap-2,
    .navbar, .footer, .alert {
        display: none !important;
    }

    .main-content {
        margin: 0;
        padding: 0;
        background: white !important;
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

    /* Summary Cards */
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
