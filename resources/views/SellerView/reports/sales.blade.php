@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h2 class="fw-bold m-0 text-dark">Laporan Penjualan</h2>
            <p class="text-muted">Ringkasan transaksi penjualan produk Anda secara permanen.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-dark rounded-pill px-4" onclick="window.print()">
                <i class="bi bi-printer me-2"></i>Cetak
            </button>
            <button class="btn btn-emerald rounded-pill px-4" onclick="exportPDF()">
                <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
            </button>
        </div>
    </div>
</div>

{{-- FILTER SECTION --}}
<div class="card card-modern p-4 mb-5 border-0">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label fw-bold small text-muted text-uppercase ls-1">Tanggal Mulai</label>
            <input type="date" name="start_date" class="form-control border-0 bg-light rounded-3" value="{{ $startDate }}">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold small text-muted text-uppercase ls-1">Tanggal Akhir</label>
            <input type="date" name="end_date" class="form-control border-0 bg-light rounded-3" value="{{ $endDate }}">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-emerald w-100">
                <i class="bi bi-filter me-2"></i>Terapkan Filter
            </button>
        </div>
    </form>
</div>

{{-- SUMMARY CARDS --}}
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card card-modern p-4 border-0">
            <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-1">Total Penjualan</small>
            <h3 class="fw-bold m-0">Rp {{ number_format($totalSales, 0, ',', '.') }}</h3>
            <span class="text-success small fw-semibold mt-2 d-block">
                <i class="bi bi-bag-check me-1"></i> {{ $totalOrders }} pesanan selesai
            </span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-modern p-4 border-0">
            <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-1">Rata-rata Transaksi</small>
            <h3 class="fw-bold m-0">Rp {{ $totalOrders > 0 ? number_format($totalSales / $totalOrders, 0, ',', '.') : '0' }}</h3>
            <span class="text-primary small fw-semibold mt-2 d-block">
                <i class="bi bi-graph-up me-1"></i> Per pesanan
            </span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-modern p-4 border-0 bg-emerald text-white" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">
            <small class="text-white-50 text-uppercase fw-bold ls-1 d-block mb-1">Produk Terlaris</small>
            <h4 class="fw-bold m-0">{{ Str::limit($topProducts->first()['nama_produk'] ?? '-', 25) }}</h4>
            <span class="text-white-50 small fw-semibold mt-2 d-block">
                <i class="bi bi-star-fill me-1"></i> {{ $topProducts->first()['quantity'] ?? 0 }} item terjual
            </span>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- DETAIL LIST --}}
    <div class="col-md-8">
        <h5 class="fw-bold mb-4">Detail Riwayat Penjualan</h5>
        
        {{-- CARD LAPORAN FORMAL (Preview) --}}
        <div class="card card-modern mb-5 border-0 overflow-hidden">
            <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold m-0">Laporan Formal (Preview Cetak)</h6>
                    <small class="text-muted">Format resmi untuk dokumen cetak</small>
                </div>
                <i class="bi bi-file-earmark-text text-muted fs-4"></i>
            </div>
            <div class="card-body p-5">
                <div class="text-center mb-5">
                    <h5 class="fw-bold mb-1">CAMPIFY MARKETPLACE</h5>
                    <h4 class="fw-bold text-uppercase border-bottom pb-3 d-inline-block px-4">LAPORAN PENJUALAN PRODUK</h4>
                    <div class="mt-3 small text-muted">
                        Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="bg-light">
                            <tr class="text-center">
                                <th width="5%">No</th>
                                <th width="15%">Tanggal</th>
                                <th width="20%">Pembeli</th>
                                <th width="40%">Produk</th>
                                <th width="20%">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach($orders as $order)
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                <td>{{ $order->buyer->name ?? '-' }}</td>
                                <td>
                                    @foreach($order->details as $detail)
                                        • {{ $detail->product->nama_produk ?? '-' }} ({{ $detail->qty }}x)<br>
                                    @endforeach
                                </td>
                                <td class="text-end fw-bold">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <th colspan="4" class="text-end py-2">TOTAL PENDAPATAN</th>
                                <th class="text-end py-2">Rp {{ number_format($totalSales, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        @foreach($orders as $order)
        <div class="card card-modern p-4 mb-3 border-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-light rounded-circle fs-5">👤</div>
                    <div>
                        <h6 class="fw-bold m-0">{{ $order->buyer->name ?? 'User' }}</h6>
                        <small class="text-muted">{{ $order->created_at->format('d M Y, H:i') }}</small>
                    </div>
                </div>
                <div class="text-end">
                    <h5 class="fw-bold text-emerald m-0">Rp {{ number_format($order->total, 0, ',', '.') }}</h5>
                    <span class="badge bg-emerald-soft text-emerald rounded-pill px-3 mt-1">Selesai</span>
                </div>
            </div>
            <div class="border-top pt-3 mt-2">
                @foreach($order->details as $detail)
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small">{{ $detail->product->nama_produk ?? '-' }} ({{ $detail->qty }}x)</span>
                    <span class="small fw-semibold text-muted">Rp {{ number_format($detail->harga * $detail->qty, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        @if($orders->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-journal-x fs-1 text-muted d-block mb-3"></i>
            <p class="text-muted">Belum ada transaksi penjualan di periode ini</p>
        </div>
        @endif
    </div>

    {{-- BEST PRODUCTS --}}
    <div class="col-md-4">
        <div class="card card-modern p-4 border-0 position-sticky" style="top: 100px;">
            <h5 class="fw-bold mb-4">Top Performance</h5>
            <p class="text-muted small mb-4">Produk dengan tingkat penjualan tertinggi periode ini.</p>

            @foreach($topProducts as $product)
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-light rounded-4 p-3 fs-5">📦</div>
                <div class="flex-grow-1 overflow-hidden">
                    <h6 class="fw-bold m-0 text-truncate">{{ $product['nama_produk'] }}</h6>
                    <small class="text-muted">{{ $product['quantity'] }} item terjual</small>
                </div>
                <div class="text-end">
                    <span class="badge bg-emerald-soft text-emerald rounded-pill px-2">#{{ $loop->iteration }}</span>
                </div>
            </div>
            @endforeach

            @if($topProducts->isEmpty())
            <p class="text-muted small text-center">Belum ada data performa</p>
            @endif
        </div>
    </div>
</div>

{{-- PRINT STYLES --}}
<style>
    @media print {
        .sidebar, .navbar, .btn, form, .card-modern:not(.overflow-hidden), h2, p, .no-print {
            display: none !important;
        }
        .main-content { margin: 0 !important; padding: 0 !important; }
        .card-modern.overflow-hidden { box-shadow: none !important; transform: none !important; border: none !important; }
        body { background: white !important; }
    }
    .ls-1 { letter-spacing: 1px; }
    .text-emerald { color: #10B981; }
    .bg-emerald-soft { background-color: #ecfdf5; }
</style>

<script>
    function exportPDF() { window.print(); }
</script>
@endsection
