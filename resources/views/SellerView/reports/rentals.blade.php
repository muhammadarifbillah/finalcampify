@extends('SellerView.layouts.app_seller')

@section('content')
<div class="dashboard-header mb-5">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h2 class="fw-bold m-0 text-dark">Laporan Penyewaan</h2>
            <p class="text-muted">Ringkasan transaksi penyewaan alat dan perlengkapan camping Anda.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-dark rounded-pill px-4" onclick="window.print()">
                <i class="bi bi-printer me-2"></i>Cetak
            </button>
            <button class="btn btn-primary rounded-pill px-4 border-0" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);" onclick="exportPDF()">
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
            <button type="submit" class="btn btn-primary w-100 border-0 rounded-3 py-2" style="background: #3b82f6;">
                <i class="bi bi-filter me-2"></i>Terapkan Filter
            </button>
        </div>
    </form>
</div>

{{-- SUMMARY CARDS --}}
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card card-modern p-4 border-0">
            <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-1">Total Pendapatan Sewa</small>
            <h3 class="fw-bold m-0 text-primary">Rp {{ number_format($totalRentalIncome, 0, ',', '.') }}</h3>
            <span class="text-primary small fw-semibold mt-2 d-block">
                <i class="bi bi-calendar-check me-1"></i> {{ $totalRentals }} transaksi sewa
            </span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-modern p-4 border-0">
            <small class="text-muted text-uppercase fw-bold ls-1 d-block mb-1">Rata-rata Sewa</small>
            <h3 class="fw-bold m-0">Rp {{ $totalRentals > 0 ? number_format($totalRentalIncome / $totalRentals, 0, ',', '.') : '0' }}</h3>
            <span class="text-muted small fw-semibold mt-2 d-block">
                <i class="bi bi-calculator me-1"></i> Per penyewaan
            </span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-modern p-4 border-0 text-white" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
            <small class="text-white-50 text-uppercase fw-bold ls-1 d-block mb-1">Produk Terfavorit</small>
            <h4 class="fw-bold m-0 text-truncate">{{ $topRentedProducts->first()['nama_produk'] ?? '-' }}</h4>
            <span class="text-white-50 small fw-semibold mt-2 d-block">
                <i class="bi bi-fire me-1"></i> Disewa {{ $topRentedProducts->first()['count'] ?? 0 }} kali
            </span>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- DETAIL LIST --}}
    <div class="col-md-8">
        <h5 class="fw-bold mb-4">Detail Riwayat Penyewaan</h5>
        
        {{-- CARD LAPORAN FORMAL (Preview) --}}
        <div class="card card-modern mb-5 border-0 overflow-hidden">
            <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold m-0">Laporan Formal (Preview Cetak)</h6>
                    <small class="text-muted">Format resmi untuk dokumen cetak</small>
                </div>
                <i class="bi bi-file-earmark-medical text-muted fs-4"></i>
            </div>
            <div class="card-body p-5">
                <div class="text-center mb-5">
                    <h5 class="fw-bold mb-1">CAMPIFY MARKETPLACE</h5>
                    <h4 class="fw-bold text-uppercase border-bottom pb-3 d-inline-block px-4">LAPORAN PENYEWAAN ALAT</h4>
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
                                <th width="20%">Penyewa</th>
                                <th width="30%">Produk</th>
                                <th width="10%">Durasi</th>
                                <th width="20%">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach($rentals as $rental)
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td>{{ $rental->created_at->format('d/m/Y') }}</td>
                                <td>{{ $rental->user->name ?? '-' }}</td>
                                <td>{{ $rental->product->nama_produk ?? '-' }}</td>
                                <td class="text-center">{{ $rental->duration }} Hari</td>
                                <td class="text-end fw-bold">Rp {{ number_format($rental->price * $rental->duration, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <th colspan="5" class="text-end py-2 text-primary">TOTAL PENDAPATAN SEWA</th>
                                <th class="text-end py-2 text-primary">Rp {{ number_format($totalRentalIncome, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        @foreach($rentals as $rental)
        <div class="card card-modern p-4 mb-3 border-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-primary bg-opacity-10 rounded-circle fs-5">🏕️</div>
                    <div>
                        <h6 class="fw-bold m-0">{{ $rental->user->name ?? 'User' }}</h6>
                        <small class="text-muted">{{ $rental->product->nama_produk ?? '-' }}</small>
                    </div>
                </div>
                <div class="text-end">
                    <h5 class="fw-bold text-primary m-0">Rp {{ number_format($rental->price * $rental->duration, 0, ',', '.') }}</h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 mt-1">Completed</span>
                </div>
            </div>
            <div class="border-top pt-3 mt-2 d-flex justify-content-between text-muted small">
                <span><i class="bi bi-calendar-event me-1"></i> {{ optional($rental->start_date)->format('d M') }} - {{ optional($rental->end_date)->format('d M Y') }}</span>
                <span><i class="bi bi-clock me-1"></i> Durasi: <strong>{{ $rental->duration }} Hari</strong></span>
                <span><i class="bi bi-tag me-1"></i> Rp {{ number_format($rental->price, 0, ',', '.') }}/Hari</span>
            </div>
        </div>
        @endforeach

        @if($rentals->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-calendar-x fs-1 text-muted d-block mb-3"></i>
            <p class="text-muted">Belum ada riwayat penyewaan di periode ini</p>
        </div>
        @endif
    </div>

    {{-- BEST PRODUCTS --}}
    <div class="col-md-4">
        <div class="card card-modern p-4 border-0 position-sticky" style="top: 100px;">
            <h5 class="fw-bold mb-4">Top Rented Gear</h5>
            <p class="text-muted small mb-4">Peralatan yang paling sering disewa oleh pelanggan Anda.</p>

            @foreach($topRentedProducts as $product)
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-primary bg-opacity-10 text-primary rounded-4 p-3 fs-5">🏕️</div>
                <div class="flex-grow-1 overflow-hidden">
                    <h6 class="fw-bold m-0 text-truncate">{{ $product['nama_produk'] }}</h6>
                    <small class="text-muted">{{ $product['count'] }} kali disewa</small>
                </div>
                <div class="text-end">
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2">#{{ $loop->iteration }}</span>
                </div>
            </div>
            @endforeach

            @if($topRentedProducts->isEmpty())
            <p class="text-muted small text-center">Belum ada data penyewaan</p>
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
</style>

<script>
    function exportPDF() { window.print(); }
</script>
@endsection