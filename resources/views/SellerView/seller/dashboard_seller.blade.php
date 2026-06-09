@extends('SellerView.layouts.app_seller')

@section('content')

{{-- BANNER TOKO DI-BAN --}}
@if(isset($store) && $store && in_array($store->status, ['banned', 'suspended', 'rejected']))
@php
    $isBanned    = $store->status === 'banned';
    $isSuspended = $store->status === 'suspended';
    $statusLabel = $isBanned ? 'DI-BAN' : ($isSuspended ? 'DISUSPEND' : 'DITOLAK');
    $sudahKlarifikasi = $store->catatan_admin && str_starts_with($store->catatan_admin, 'Klarifikasi Seller:');
    $isiKlarifikasi   = $sudahKlarifikasi ? substr($store->catatan_admin, strlen('Klarifikasi Seller: ')) : '';
    $colorClass = $isBanned ? 'danger' : ($isSuspended ? 'warning' : 'secondary');
@endphp

<div class="card card-modern mb-5 border-0 ban-card ban-{{ $colorClass }}">
    {{-- Bagian atas: Info --}}
    <div class="card-body p-4">
        <div class="d-flex align-items-start gap-3">
            <div class="ban-icon-circle ban-icon-{{ $colorClass }}">
                <i class="bi bi-slash-circle fs-4"></i>
            </div>
            <div>
                <div class="mb-2">
                    <span class="badge bg-{{ $colorClass }} text-uppercase fw-bold" style="letter-spacing: .5px; font-size: .7rem;">
                        TOKO {{ $statusLabel }}
                    </span>
                    @if($sudahKlarifikasi)
                        <span class="badge bg-info ms-1" style="font-size: .65rem;">✓ Klarifikasi Terkirim</span>
                    @endif
                </div>
                <h5 class="fw-bold text-dark mb-1">Toko Anda tidak dapat beroperasi saat ini</h5>
                <p class="text-muted small mb-0">
                    @if($isBanned)
                        Toko <strong>{{ $store->nama_toko }}</strong> telah diblokir oleh admin. Anda tidak dapat menerima pesanan baru selama status ini aktif.
                    @elseif($isSuspended)
                        Toko <strong>{{ $store->nama_toko }}</strong> sedang disuspend sementara oleh admin.
                    @else
                        Pendaftaran toko <strong>{{ $store->nama_toko }}</strong> ditolak oleh admin.
                    @endif
                </p>

                @if($store->catatan_admin && !$sudahKlarifikasi)
                <div class="mt-3 p-3 bg-light rounded-3 border">
                    <small class="fw-semibold text-muted d-block mb-1">Alasan dari Admin:</small>
                    <span class="small text-dark">{{ $store->catatan_admin }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <hr class="m-0 text-light">

    {{-- Bagian bawah: Form Klarifikasi --}}
    <div class="card-body p-4">
        @if(session('success'))
            <div class="alert alert-success alert-modern d-flex align-items-center gap-2 small mb-3">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        @if($sudahKlarifikasi)
            <div class="alert alert-success alert-modern small mb-3">
                <p class="fw-bold mb-1"><i class="bi bi-send-check-fill me-1"></i>Klarifikasi Anda telah dikirim ke admin:</p>
                <p class="text-muted mb-0" style="white-space: pre-line;">{{ $isiKlarifikasi }}</p>
            </div>
            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 mb-3"
                    onclick="document.getElementById('editKlarifikasiForm').classList.toggle('d-none')">
                <i class="bi bi-pencil me-1"></i>Edit Klarifikasi
            </button>
            <div id="editKlarifikasiForm" class="d-none">
        @endif

        <form method="POST" action="{{ route('seller.store-profile.klarifikasi') }}">
            @csrf
            <label class="form-label fw-semibold small">
                <i class="bi bi-envelope me-1 text-{{ $colorClass }}"></i>
                {{ $sudahKlarifikasi ? 'Perbarui Klarifikasi' : 'Kirim Klarifikasi ke Admin' }}
            </label>
            <textarea name="klarifikasi" rows="3" required minlength="20" maxlength="1000"
                      class="form-control rounded-3 mb-2 @error('klarifikasi') is-invalid @enderror"
                      placeholder="Jelaskan mengapa toko Anda seharusnya dibuka kembali. Sertakan informasi relevan yang dapat membantu admin meninjau kasus Anda..."
                      style="resize: none; font-size: .88rem;">{{ old('klarifikasi', $sudahKlarifikasi ? $isiKlarifikasi : '') }}</textarea>
            @error('klarifikasi')
                <div class="invalid-feedback d-block small">{{ $message }}</div>
            @enderror
            <div class="d-flex align-items-center justify-content-between mt-1">
                <small class="text-muted">Minimal 20 karakter. Admin akan membalas melalui email atau membuka ban.</small>
                <button type="submit" class="btn btn-{{ $colorClass }} btn-sm text-white rounded-pill px-4 fw-semibold flex-shrink-0">
                    <i class="bi bi-send me-1"></i>{{ $sudahKlarifikasi ? 'Perbarui' : 'Kirim Klarifikasi' }}
                </button>
            </div>
        </form>

        @if($sudahKlarifikasi)
            </div>
        @endif
    </div>
</div>

<style>
    .ban-card { border-left: 5px solid transparent !important; border-radius: 20px; }
    .ban-danger  { border-left-color: var(--bs-danger) !important; }
    .ban-warning { border-left-color: var(--bs-warning) !important; }
    .ban-secondary { border-left-color: var(--bs-secondary) !important; }
    .ban-icon-circle {
        width: 48px; height: 48px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .ban-icon-danger   { background: #fee2e2; color: var(--bs-danger); }
    .ban-icon-warning  { background: #fef3c7; color: var(--bs-warning); }
    .ban-icon-secondary { background: #f3f4f6; color: var(--bs-secondary); }
</style>
@endif

{{-- HEADER DASHBOARD --}}
<div class="dashboard-header mb-5">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold m-0 text-dark">Dashboard Overview</h2>
            <p class="text-muted">Selamat datang kembali! Berikut adalah ringkasan performa toko Anda hari ini.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="d-flex gap-2 justify-content-md-end">
                <a href="{{ route('seller.products.create') }}" class="btn btn-emerald rounded-pill px-4 shadow-sm">
                    <i class="bi bi-plus-lg me-2"></i>Tambah Produk
                </a>
                <button class="btn btn-light rounded-circle shadow-sm p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;" onclick="window.location.reload()">
                    <i class="bi bi-arrow-clockwise fs-5"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- STATS CARDS --}}
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-5">
    {{-- Revenue --}}
    <div class="col">
        <div class="card card-modern p-4 h-100 border-0 shadow-sm position-relative overflow-hidden">
            <div class="position-absolute top-0 end-0 p-3 opacity-10 fs-1 text-emerald">💰</div>
            <small class="text-muted text-uppercase fw-bold ls-1 mb-2 d-block" style="font-size: 0.7rem;">Total Pendapatan</small>
            <h3 class="fw-bold mb-2 text-dark">Rp {{ number_format($totalRevenue,0,',','.') }}</h3>
            @if($trendUp)
                <span class="badge bg-emerald-soft text-emerald rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                    <i class="bi bi-arrow-up-right me-1"></i> +12.5% bln ini
                </span>
            @else
                <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                    <i class="bi bi-arrow-down-right me-1"></i> -3.2% bln ini
                </span>
            @endif
        </div>
    </div>

    {{-- Sales Payout --}}
    <div class="col">
        <a href="{{ route('seller.reports.payouts') }}" class="text-decoration-none">
            <div class="card card-modern p-4 h-100 border-0 shadow-sm position-relative overflow-hidden">
                <div class="position-absolute top-0 end-0 p-3 opacity-10 fs-1 text-success">Rp</div>
                <small class="text-muted text-uppercase fw-bold ls-1 mb-2 d-block" style="font-size: 0.7rem;">Dana Jual Dicairkan</small>
                <h3 class="fw-bold mb-2 text-dark">Rp {{ number_format($totalSalesDisbursed,0,',','.') }}</h3>
                <span class="text-success small fw-semibold">
                    Masuk dari pencairan admin
                </span>
            </div>
        </a>
    </div>

    {{-- Waiting Payout --}}
    <div class="col">
        <a href="{{ route('seller.reports.payouts') }}" class="text-decoration-none">
            <div class="card card-modern p-4 h-100 border-0 shadow-sm position-relative overflow-hidden">
                <div class="position-absolute top-0 end-0 p-3 opacity-10 fs-1 text-warning">Rp</div>
                <small class="text-muted text-uppercase fw-bold ls-1 mb-2 d-block" style="font-size: 0.7rem;">Menunggu Pencairan</small>
                <h3 class="fw-bold mb-2 text-dark">Rp {{ number_format($totalSalesWaitingPayout,0,',','.') }}</h3>
                <span class="text-warning small fw-semibold">
                    Order selesai belum cair
                </span>
            </div>
        </a>
    </div>

    {{-- Orders --}}
    <div class="col">
        <div class="card card-modern p-4 h-100 border-0 shadow-sm position-relative overflow-hidden">
            <div class="position-absolute top-0 end-0 p-3 opacity-10 fs-1 text-warning">🧾</div>
            <small class="text-muted text-uppercase fw-bold ls-1 mb-2 d-block" style="font-size: 0.7rem;">Pesanan Berjalan</small>
            <h3 class="fw-bold mb-2 text-dark">{{ $orders->count() }}</h3>
            <span class="text-muted small">
                <strong class="text-danger">{{ $pendingOrdersCount }}</strong> butuh dikirim
            </span>
        </div>
    </div>

    {{-- Barang Terjual --}}
    <div class="col">
        <div class="card card-modern p-4 h-100 border-0 shadow-sm position-relative overflow-hidden">
            <div class="position-absolute top-0 end-0 p-3 opacity-10 fs-1 text-info">🛍️</div>
            <small class="text-muted text-uppercase fw-bold ls-1 mb-2 d-block" style="font-size: 0.7rem;">Barang Terjual</small>
            <h3 class="fw-bold mb-2 text-dark">{{ $soldItemsCount }}</h3>
            <span class="text-info small fw-semibold">
                Unit berhasil dibeli
            </span>
        </div>
    </div>

    {{-- Rented Gear --}}
    <div class="col">
        <div class="card card-modern p-4 h-100 border-0 shadow-sm position-relative overflow-hidden">
            <div class="position-absolute top-0 end-0 p-3 opacity-10 fs-1 text-primary">⛺</div>
            <small class="text-muted text-uppercase fw-bold ls-1 mb-2 d-block" style="font-size: 0.7rem;">Alat Disewa</small>
            <h3 class="fw-bold mb-2 text-dark">{{ $rentedGearCount }}</h3>
            <span class="text-primary small fw-semibold">
                Unit aktif di lapangan
            </span>
        </div>
    </div>

    {{-- Completed Rental Funds --}}
    <div class="col">
        <div class="card card-modern p-4 h-100 border-0 shadow-sm position-relative overflow-hidden">
            <div class="position-absolute top-0 end-0 p-3 opacity-10 fs-1 text-success">🏕️</div>
            <small class="text-muted text-uppercase fw-bold ls-1 mb-2 d-block" style="font-size: 0.7rem;">Dana Sewa Bersih</small>
            <h3 class="fw-bold mb-2 text-dark">Rp {{ number_format($completedRentalFunds,0,',','.') }}</h3>
            <span class="text-success small fw-semibold">
                Diterima dari Admin
            </span>
        </div>
    </div>

    {{-- Admin Fee Funds --}}
    <div class="col">
        <div class="card card-modern p-4 h-100 border-0 shadow-sm position-relative overflow-hidden">
            <div class="position-absolute top-0 end-0 p-3 opacity-10 fs-1 text-danger">🛡️</div>
            <small class="text-muted text-uppercase fw-bold ls-1 mb-2 d-block" style="font-size: 0.7rem;">Potongan Admin</small>
            <h3 class="fw-bold mb-2 text-dark">Rp {{ number_format($totalAdminFunds,0,',','.') }}</h3>
            <span class="text-danger small fw-semibold">
                Fee Platform (10%)
            </span>
        </div>
    </div>

    {{-- Rating --}}
    <div class="col">
        <div class="card card-modern p-4 h-100 border-0 shadow-sm position-relative overflow-hidden" style="background: linear-gradient(135deg, #111827 0%, #1f2937 100%);">
            <div class="position-absolute top-0 end-0 p-3 opacity-20 fs-1 text-warning">⭐</div>
            <small class="text-white-50 text-uppercase fw-bold ls-1 mb-2 d-block" style="font-size: 0.7rem;">Reputasi Toko</small>
            <h3 class="fw-bold mb-2 text-white">{{ number_format($avgProductRating, 1) }}<span class="fs-6 text-white-50">/5.0</span></h3>
            <div class="d-flex gap-1 text-warning">
                @for($i=1; $i<=5; $i++)
                    <i class="bi bi-star{{ $i <= round($avgProductRating) ? '-fill' : '' }}"></i>
                @endfor
            </div>
        </div>
    </div>
</div>

{{-- NOTIFIKASI PENYEWAAN BARU --}}
@if($totalRentalRequestsCount > 0)
<div class="card card-modern mb-5 border-0 bg-emerald-soft" style="border-left: 5px solid var(--primary-emerald) !important;">
    <div class="card-body p-4 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <div class="icon-box bg-white rounded-circle p-3 me-4 shadow-sm text-emerald fs-4 d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                <i class="bi bi-bell-fill"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-1 text-dark">Permintaan Sewa Baru!</h5>
                <p class="text-muted mb-0 small">Ada <strong>{{ $totalRentalRequestsCount }}</strong> penyewaan alat yang menunggu konfirmasi Anda.</p>
            </div>
        </div>
        <a href="/seller/rentals" class="btn btn-emerald px-4 rounded-pill fw-bold shadow-sm">Proses Sekarang</a>
    </div>
</div>
@endif

<div class="row g-4">
    {{-- CHART PENJUALAN --}}
    <div class="col-lg-8">
        <div class="card card-modern p-5 border-0 shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h5 class="fw-bold m-0 text-dark">Grafik Penjualan</h5>
                    <small class="text-muted">Statistik pendapatan 7 hari terakhir</small>
                </div>
                <div class="badge bg-light text-muted p-2 rounded-pill px-3 border fw-semibold">{{ now()->format('M Y') }}</div>
            </div>
            <div style="height: 350px;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    {{-- PERFORMA & SCORE --}}
    <div class="col-lg-4">
        <div class="card card-modern p-5 border-0 shadow-sm h-100">
            <h5 class="fw-bold mb-5 text-dark">Seller Health</h5>
            
            <div class="performance-item mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Kualitas Layanan</span>
                    <span class="fw-bold small text-emerald">{{ $qualityScore }}%</span>
                </div>
                <div class="progress rounded-pill shadow-none border" style="height: 10px; background: #f1f5f9;">
                    <div class="progress-bar bg-emerald" style="width: {{ $qualityScore }}%"></div>
                </div>
            </div>

            <div class="performance-item mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Kecepatan Balas Chat</span>
                    <span class="fw-bold small text-primary">{{ $chatScore }}%</span>
                </div>
                <div class="progress rounded-pill shadow-none border" style="height: 10px; background: #f1f5f9;">
                    <div class="progress-bar bg-primary" style="width: {{ $chatScore }}%"></div>
                </div>
            </div>

            <div class="performance-item mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Akurasi Stok</span>
                    <span class="fw-bold small text-warning">{{ $stockScore }}%</span>
                </div>
                <div class="progress rounded-pill shadow-none border" style="height: 10px; background: #f1f5f9;">
                    <div class="progress-bar bg-warning" style="width: {{ $stockScore }}%"></div>
                </div>
            </div>

            <div class="p-4 bg-light rounded-4 border dashed mt-4">
                <h6 class="fw-bold mb-2 text-dark small"><i class="bi bi-lightbulb me-2 text-warning"></i>Tips Hari Ini</h6>
                <p class="small text-muted m-0 leading-relaxed">Balas chat pembeli dalam < 10 menit untuk meningkatkan conversion rate Anda.</p>
            </div>
        </div>
    </div>
</div>

{{-- TABEL PESANAN TERBARU --}}
<div class="card card-modern mt-5 border-0 p-5 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h5 class="fw-bold m-0 text-dark">Pesanan Terbaru</h5>
            <small class="text-muted">Transaksi terakhir yang masuk ke toko Anda</small>
        </div>
        <a href="/seller/orders" class="btn btn-light rounded-pill px-4 fw-bold small border text-muted">
            Lihat Semua Pesanan
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle custom-table">
            <thead class="bg-light bg-opacity-50">
                <tr>
                    <th class="border-0 px-4 py-3 text-muted small fw-bold text-uppercase ls-1">Order ID</th>
                    <th class="border-0 px-4 py-3 text-muted small fw-bold text-uppercase ls-1">Item</th>
                    <th class="border-0 px-4 py-3 text-muted small fw-bold text-uppercase ls-1">Buyer</th>
                    <th class="border-0 px-4 py-3 text-muted small fw-bold text-uppercase ls-1 text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders->take(5) as $o)
                <tr style="cursor: pointer;" onclick="window.location='/seller/orders/{{ $o->id }}'">
                    <td class="px-4 py-4 fw-bold text-dark">{{ $o->seller_order_number ?? '#'.$o->id }}</td>
                    <td class="px-4 py-4">
                        <div class="d-flex align-items-center gap-3">
                            @php
                                $firstDetail = $o->details->first();
                                $p = $firstDetail ? $firstDetail->product : null;
                                $imgPath = $p ? ($p->image ?? $p->gambar) : null;
                                if ($imgPath && !str_starts_with($imgPath, 'assets/images/') && !str_starts_with($imgPath, 'storage/') && !str_starts_with($imgPath, 'http')) {
                                    if (file_exists(public_path('assets/images/' . $imgPath))) {
                                        $imgPath = 'assets/images/' . $imgPath;
                                    } else {
                                        $imgPath = 'storage/' . $imgPath;
                                    }
                                }
                            @endphp
                            
                            <div class="p-1 bg-light rounded-3 overflow-hidden" style="width: 45px; height: 45px;">
                                @if($imgPath && (file_exists(public_path($imgPath)) || str_contains($imgPath, 'http')))
                                    <img src="{{ asset($imgPath) }}" class="w-100 h-100 object-cover rounded">
                                @else
                                    <div class="w-100 h-100 bg-emerald-soft rounded d-flex align-items-center justify-center text-emerald">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="fw-bold text-dark small">{{ Str::limit($p->nama_produk ?? '-', 35) }}</div>
                                <small class="text-muted">{{ $firstDetail->qty ?? 0 }} unit</small>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-dark small fw-semibold">{{ $o->buyer->name ?? $o->buyer_name }}</td>
                    <td class="px-4 py-4 text-center">
                        @php
                            $isRental = optional($o->details->first())->type === 'rent';
                            $displayStatus = $isRental && $o->rental ? $o->rental->status : $o->status;
                            
                            $statusLabelClass = match($displayStatus) {
                                'delivered', 'selesai', 'completed', 'active', 'aktif' => 'bg-emerald-soft text-emerald',
                                'diproses', 'processing', 'pending' => 'bg-info-subtle text-info',
                                'menunggu' => 'bg-warning-subtle text-warning',
                                'shipped', 'dikirim' => 'bg-primary-subtle text-primary',
                                'dibatalkan', 'cancelled' => 'bg-danger-subtle text-danger',
                                default => 'bg-light text-muted'
                            };
                        @endphp
                        <span class="badge rounded-pill px-3 py-2 fw-bold text-uppercase ls-1 {{ $statusLabelClass }}" style="font-size: 0.65rem;">
                            {{ $o->status_label ?? $displayStatus }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="opacity-25 mb-3 fs-1">📦</div>
                        <p class="text-muted">Belum ada pesanan terbaru saat ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Dashboard Init');
        const labels = @json($labels);
        const salesData = @json($dataSales);
        
        console.log('Chart Labels:', labels);
        console.log('Chart Data:', salesData);

        const ctx = document.getElementById('salesChart');
        if(ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: salesData,
                        fill: true,
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderColor: '#10B981',
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#10B981',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#111827',
                            titleFont: { size: 13 },
                            bodyFont: { size: 13 },
                            padding: 12,
                            cornerRadius: 10,
                            callbacks: {
                                label: function(context) {
                                    return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { borderDash: [5, 5], color: '#f1f5f9', drawBorder: false },
                            ticks: { 
                                color: '#94a3b8',
                                font: { size: 11 },
                                callback: function(value) { 
                                    if (value >= 1000000) return 'Rp ' + (value/1000000).toFixed(1) + 'jt';
                                    if (value >= 1000) return 'Rp ' + (value/1000) + 'rb';
                                    return 'Rp ' + value;
                                }
                            }
                        },
                        x: { 
                            grid: { display: false, drawBorder: false }, 
                            ticks: { color: '#94a3b8', font: { size: 11 } } 
                        }
                    }
                }
            });
        }
    });
</script>

<style>
    .ls-1 { letter-spacing: 1px; }
    .bg-emerald-soft { background-color: #ecfdf5; }
    .bg-emerald { background-color: #10B981 !important; }
    .text-emerald { color: #10B981 !important; }
    .border-emerald { border-color: #10B981 !important; }
    .dashed { border: 2px dashed #e2e8f0 !important; }
    .leading-relaxed { line-height: 1.6; }
    .custom-table tr { transition: all 0.2s ease; }
    .custom-table tr:hover { background-color: #f8fafc !important; }
    .bg-info-subtle { background-color: #f0f9ff; }
    .bg-warning-subtle { background-color: #fffbeb; }
</style>
@endsection
