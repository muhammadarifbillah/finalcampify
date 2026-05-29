@extends('layouts.admin')

@section('title', 'Detail Toko')

@php
    $statusBadge = fn ($status) => match ($status) {
        'active', 'approved' => 'admin-badge-success',
        'pending', 'waiting' => 'admin-badge-warning',
        'rejected', 'banned' => 'admin-badge-danger',
        'suspended' => 'admin-badge-info',
        default => 'admin-badge-muted',
    };
@endphp

@section('content')
    <div class="space-y-8">

        {{-- Header --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="admin-section-title">{{ $store->nama_toko }}</h1>
                <p class="admin-section-subtitle">{{ $store->user->name ?? '-' }} &mdash; {{ $store->user->email ?? '-' }}</p>
            </div>
            <a href="{{ route('admin.stores.index') }}" class="admin-button admin-button-ghost">
                <i data-lucide="arrow-left"></i>
                Kembali
            </a>
        </div>

        {{-- ⚠️ ALERT: Toko Mencurigakan --}}
        @if($isSuspicious)
        <div class="flex items-start gap-4 rounded-xl border-2 border-red-400 bg-red-50 p-5 shadow">
            <div class="mt-0.5 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-red-500 text-white">
                <i data-lucide="shield-alert" class="h-5 w-5"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-extrabold text-red-700">⚠ Toko Ini Terindikasi Mencurigakan</h3>
                <p class="mt-1 text-sm text-red-600">
                    Toko <strong>{{ $store->nama_toko }}</strong> telah menerima
                    <strong>{{ $returnCount }} pengajuan retur</strong> dari pembeli
                    (batas kewajaran: {{ $suspiciousThreshold }}).
                    Terlalu banyak retur dapat mengindikasikan penipuan, produk palsu, atau kecurangan penjual.
                </p>
                @if($topReasons->isNotEmpty())
                <div class="mt-3">
                    <p class="text-xs font-bold uppercase tracking-wider text-red-500">Alasan Retur Terbanyak:</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach($topReasons as $reason => $count)
                            <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 ring-1 ring-red-300">
                                <i data-lucide="message-square" class="h-3 w-3"></i>
                                {{ Str::limit($reason, 50) }}
                                <span class="ml-1 rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] font-bold text-white">×{{ $count }}</span>
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            <div class="flex-shrink-0">
                @if($store->status !== 'banned')
                <form method="POST" action="{{ route('admin.stores.ban', $store->id) }}">
                    @csrf
                    <input type="hidden" name="reason" value="Otomatis: terlalu banyak retur ({{ $returnCount }} kali). Harap investigasi.">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white shadow hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400"
                        onclick="return confirm('Ban toko {{ $store->nama_toko }} karena mencurigakan?')">
                        <i data-lucide="ban" class="h-4 w-4"></i>
                        Ban Sekarang
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endif

        {{-- Stats --}}
        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-6">
            <div class="admin-card admin-stat-card">
                <p class="admin-stat-label">Total Produk</p>
                <h2 class="admin-stat-value">{{ $stats['total_products'] }}</h2>
                <p class="admin-stat-meta">Semua status</p>
            </div>
            <div class="admin-card admin-stat-card">
                <p class="admin-stat-label">Approved</p>
                <h2 class="admin-stat-value">{{ $stats['approved_products'] }}</h2>
                <p class="admin-stat-meta">Tampil ke buyer</p>
            </div>
            <div class="admin-card admin-stat-card">
                <p class="admin-stat-label">Waiting</p>
                <h2 class="admin-stat-value">{{ $stats['pending_products'] }}</h2>
                <p class="admin-stat-meta">Menunggu review</p>
            </div>
            <div class="admin-card admin-stat-card">
                <p class="admin-stat-label">Rejected</p>
                <h2 class="admin-stat-value">{{ $stats['rejected_products'] }}</h2>
                <p class="admin-stat-meta">Ditolak admin</p>
            </div>
            <div class="admin-card admin-stat-card {{ $isSuspicious ? 'bg-red-700 text-white' : 'bg-amber-600 text-white' }}">
                <p class="admin-stat-label {{ $isSuspicious ? 'text-red-100' : 'text-amber-100' }}">Total Retur</p>
                <h2 class="admin-stat-value text-white">{{ $returnCount }}</h2>
                <p class="admin-stat-meta {{ $isSuspicious ? 'text-red-100' : 'text-amber-100' }}">
                    {{ $isSuspicious ? '⚠ Mencurigakan' : 'Pengajuan retur' }}
                </p>
            </div>
            <div class="admin-card admin-stat-card bg-emerald-700 text-white">
                <p class="admin-stat-label text-emerald-100">Sales</p>
                <h2 class="admin-stat-value text-white">Rp {{ number_format($stats['total_sales'], 0, ',', '.') }}</h2>
                <p class="admin-stat-meta text-emerald-100">{{ $stats['total_transactions'] }} transaksi</p>
            </div>
        </div>

        {{-- Info Toko + Aksi Seller --}}
        <div class="grid gap-6 xl:grid-cols-[1.4fr_.8fr]">
            <div class="admin-card p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-extrabold">Informasi Toko</h2>
                        <p class="mt-2 text-slate-600">{{ $store->deskripsi ?: 'Tidak ada deskripsi toko.' }}</p>
                    </div>
                    <span class="admin-badge {{ $statusBadge($store->status) }}">{{ $store->status }}</span>
                </div>
                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <div>
                        <div class="admin-stat-label">Alamat</div>
                        <p class="mt-2">{{ $store->alamat ?: '-' }}</p>
                    </div>
                    <div>
                        <div class="admin-stat-label">Bergabung</div>
                        <p class="mt-2">{{ $store->created_at?->format('d M Y') ?? '-' }}</p>
                    </div>
                    <div>
                        <div class="admin-stat-label">Terakhir Aktif</div>
                        <p class="mt-2">{{ $store->last_active?->diffForHumans() ?? '-' }}</p>
                    </div>
                    <div>
                        <div class="admin-stat-label">Catatan Admin / Klarifikasi</div>
                        @if($store->catatan_admin)
                            <p class="mt-2 rounded-lg bg-slate-50 p-2 text-sm leading-relaxed text-slate-700">{{ $store->catatan_admin }}</p>
                        @else
                            <p class="mt-2 text-slate-400">-</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Aksi Seller --}}
            <div class="admin-card p-6">
                <h2 class="text-2xl font-extrabold mb-5">Aksi Seller</h2>
                <div class="space-y-4">
                    @if($store->status === 'pending')
                        <form method="POST" action="{{ route('admin.stores.approve', $store->id) }}">
                            @csrf
                            <button class="admin-button admin-button-primary w-full" type="submit">
                                <i data-lucide="check-circle" class="h-4 w-4"></i> Approve Seller
                            </button>
                        </form>
                    @endif

                    @if(in_array($store->status, ['active', 'pending']))
                        <form method="POST" action="{{ route('admin.stores.reject', $store->id) }}" class="space-y-2">
                            @csrf
                            <textarea class="admin-form-control" name="reason" placeholder="Alasan reject..." required></textarea>
                            <button class="admin-button admin-button-danger w-full" type="submit">Reject Seller</button>
                        </form>
                    @endif

                    @if($store->status === 'active')
                        <form method="POST" action="{{ route('admin.stores.suspend', $store->id) }}" class="space-y-2">
                            @csrf
                            <textarea class="admin-form-control" name="reason" placeholder="Alasan suspend..." required></textarea>
                            <button class="admin-button admin-button-ghost w-full" type="submit">Suspend Seller</button>
                        </form>
                    @endif

                    @if($store->status !== 'banned')
                        <form method="POST" action="{{ route('admin.stores.ban', $store->id) }}" class="space-y-2">
                            @csrf
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Alasan Ban</label>
                            <textarea class="admin-form-control" name="reason" placeholder="Alasan ban seller..." required></textarea>
                            <button class="admin-button admin-button-danger w-full" type="submit"
                                onclick="return confirm('Yakin ingin ban toko ini?')">
                                <i data-lucide="ban" class="h-4 w-4"></i> Ban Seller
                            </button>
                        </form>
                    @endif

                    {{-- Unban + Simpan Klarifikasi (jika banned/suspended/rejected) --}}
                    @if(in_array($store->status, ['rejected', 'suspended', 'banned']))
                        <div class="rounded-xl border-2 border-emerald-200 bg-emerald-50 p-4 space-y-3">
                            <p class="text-sm font-bold text-emerald-700 flex items-center gap-2">
                                <i data-lucide="unlock" class="h-4 w-4"></i> Buka Ban / Aktifkan Kembali
                            </p>

                            {{-- Simpan Klarifikasi Seller (tanpa langsung unban) --}}
                            <form method="POST" action="{{ route('admin.stores.klarifikasi', $store->id) }}" class="space-y-2">
                                @csrf
                                <label class="text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Klarifikasi dari Seller
                                </label>
                                <textarea class="admin-form-control border-emerald-300 focus:border-emerald-500"
                                    name="klarifikasi_seller"
                                    rows="3"
                                    placeholder="Tuliskan klarifikasi yang diterima dari seller...">{{ old('klarifikasi_seller', $store->catatan_admin && str_starts_with($store->catatan_admin, 'Klarifikasi Seller:') ? substr($store->catatan_admin, strlen('Klarifikasi Seller: ')) : '') }}</textarea>
                                <button class="w-full rounded-lg bg-slate-200 px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-300" type="submit">
                                    <i data-lucide="save" class="h-4 w-4 inline"></i> Simpan Klarifikasi
                                </button>
                            </form>

                            {{-- Aktifkan Kembali --}}
                            <form method="POST" action="{{ route('admin.stores.activate', $store->id) }}" class="space-y-2">
                                @csrf
                                <textarea class="admin-form-control border-emerald-300"
                                    name="klarifikasi"
                                    rows="2"
                                    placeholder="Catatan tambahan saat membuka ban (opsional)..."></textarea>
                                <button class="admin-button admin-button-primary w-full" type="submit"
                                    onclick="return confirm('Yakin ingin aktifkan kembali toko ini?')">
                                    <i data-lucide="check-circle" class="h-4 w-4"></i> Aktifkan Kembali
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Validasi Produk --}}
        <div class="admin-card">
            <div class="flex flex-col gap-3 p-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-extrabold">Validasi Produk Seller</h2>
                    <p class="text-sm text-slate-500">Produk waiting/rejected tetap terlihat di seller, approved tampil ke buyer.</p>
                </div>
                <span class="admin-badge admin-badge-warning">{{ $pendingProducts->count() }} waiting</span>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>Flag</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sellerProducts as $product)
                            <tr>
                                <td>
                                    <div class="font-extrabold">{{ $product->name }}</div>
                                    <div class="text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($product->description, 70) }}</div>
                                </td>
                                <td>{{ $product->category ?? '-' }}</td>
                                <td>Rp {{ number_format($product->buy_price ?: $product->rent_price ?: $product->price, 0, ',', '.') }}</td>
                                <td><span class="admin-badge {{ $statusBadge($product->status) }}">{{ $product->status }}</span></td>
                                <td>{{ $product->flag_reason ?: '-' }}</td>
                                <td>
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.products.show', $product->id) }}" class="admin-button admin-button-ghost">Detail</a>
                                        @if(in_array($product->status, ['waiting', 'pending', 'rejected']))
                                            <form method="POST" action="{{ route('admin.stores.products.approve', [$store->id, $product->id]) }}">
                                                @csrf
                                                <button class="admin-button admin-button-primary" type="submit">Approve</button>
                                            </form>
                                        @endif
                                        @if(in_array($product->status, ['waiting', 'pending', 'approved']))
                                            <form method="POST" action="{{ route('admin.stores.products.reject', [$store->id, $product->id]) }}">
                                                @csrf
                                                <button class="admin-button admin-button-danger" type="submit">Reject</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="admin-empty">Belum ada produk seller yang terbaca.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">

            {{-- Laporan Toko --}}
            <div class="admin-card p-6">
                <h2 class="text-2xl font-extrabold mb-5">Laporan Toko &amp; Produk</h2>
                <div class="space-y-3">
                    @forelse($reports as $report)
                        <div class="rounded-lg border border-red-100 bg-red-50 p-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="admin-badge admin-badge-danger">{{ $report->type }}</span>
                                <span class="text-xs text-slate-500">{{ $report->created_at?->diffForHumans() }}</span>
                                <span class="admin-badge admin-badge-muted">{{ $report->status }}</span>
                            </div>
                            <p class="mt-3 font-extrabold">{{ $report->reason }}</p>
                            <p class="text-sm text-slate-600">{{ $report->description }}</p>
                        </div>
                    @empty
                        <div class="admin-empty">Belum ada laporan untuk toko ini.</div>
                    @endforelse
                </div>
            </div>

            {{-- Riwayat Retur — Deteksi Kenakalan Seller --}}
            <div class="admin-card p-6">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <h2 class="text-2xl font-extrabold">Riwayat Retur</h2>
                    <div class="flex items-center gap-2">
                        @if($isSuspicious)
                            <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-700 ring-1 ring-red-400">
                                <i data-lucide="alert-triangle" class="h-3 w-3"></i>
                                {{ $returnCount }} retur &mdash; MENCURIGAKAN
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                {{ $returnCount }} retur
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Top Reasons Summary --}}
                @if($topReasons->isNotEmpty())
                <div class="mb-4 rounded-xl bg-amber-50 border border-amber-200 p-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-amber-600 mb-2">
                        <i data-lucide="bar-chart-2" class="h-3 w-3 inline"></i>
                        Alasan Retur Terbanyak
                    </p>
                    <div class="space-y-1.5">
                        @php $maxCount = $topReasons->max(); @endphp
                        @foreach($topReasons as $reason => $count)
                        <div class="flex items-center gap-2">
                            <div class="flex-1">
                                <div class="flex justify-between text-xs mb-0.5">
                                    <span class="font-medium text-slate-700 truncate max-w-[200px]" title="{{ $reason }}">{{ Str::limit($reason, 45) }}</span>
                                    <span class="font-bold text-amber-700">{{ $count }}×</span>
                                </div>
                                <div class="h-1.5 w-full rounded-full bg-amber-100">
                                    <div class="h-1.5 rounded-full bg-amber-500"
                                         style="width: {{ $maxCount > 0 ? round(($count / $maxCount) * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Return List --}}
                <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                    @forelse($returns as $return)
                        <div class="rounded-lg border {{ $isSuspicious ? 'border-red-200 bg-red-50' : 'border-amber-100 bg-amber-50' }} p-4">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex flex-wrap gap-1">
                                    <span class="admin-badge {{ $return->type === 'jual_beli' ? 'admin-badge-info' : 'admin-badge-warning' }} uppercase text-[11px]">
                                        {{ $return->type === 'jual_beli' ? 'Jual Beli' : 'Sewa' }}
                                    </span>
                                    <span class="admin-badge admin-badge-muted text-[11px]">{{ $return->status }}</span>
                                </div>
                                <span class="text-xs text-slate-500">{{ $return->created_at?->format('d M Y') }}</span>
                            </div>
                            <div class="mt-3 text-sm space-y-1">
                                <div class="flex gap-1">
                                    <span class="font-bold text-slate-600 w-20 flex-shrink-0">Order:</span>
                                    <span class="text-slate-800">{{ $return->order->order_number ?? '#ORD-' . $return->order_id }}</span>
                                </div>
                                <div class="flex gap-1">
                                    <span class="font-bold text-slate-600 w-20 flex-shrink-0">Pembeli:</span>
                                    <span class="text-slate-800">{{ $return->order->buyer->name ?? '-' }}</span>
                                </div>
                                @if($return->renter_notes)
                                <div class="mt-2 rounded-lg bg-white border border-amber-200 px-3 py-2">
                                    <p class="text-xs font-bold uppercase tracking-wider text-amber-600 mb-1">
                                        <i data-lucide="message-circle" class="h-3 w-3 inline"></i>
                                        Alasan Retur:
                                    </p>
                                    <p class="text-sm text-slate-700 leading-relaxed">{{ $return->renter_notes }}</p>
                                </div>
                                @else
                                <div class="flex gap-1">
                                    <span class="font-bold text-slate-600 w-20 flex-shrink-0">Alasan:</span>
                                    <span class="text-slate-400 italic">Tidak disebutkan</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="admin-empty">Belum ada riwayat retur untuk toko ini.</div>
                    @endforelse
                </div>
            </div>

            {{-- Riwayat Aktivitas --}}
            <div class="admin-card p-6">
                <h2 class="text-2xl font-extrabold mb-5">Riwayat Aktivitas</h2>
                <div class="space-y-3">
                    @foreach($activities as $activity)
                        <div class="flex items-center gap-3 rounded-lg bg-slate-50 p-4">
                            <div class="h-2 w-2 rounded-full bg-emerald-700"></div>
                            <div>
                                <p class="font-bold">{{ $activity['message'] }}</p>
                                <p class="text-xs text-slate-500">{{ $activity['date']?->diffForHumans() ?? '-' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
@endsection
