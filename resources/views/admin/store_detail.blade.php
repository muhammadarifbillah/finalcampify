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
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="admin-section-title">{{ $store->nama_toko }}</h1>
                <p class="admin-section-subtitle">{{ $store->user->name ?? '-' }} - {{ $store->user->email ?? '-' }}</p>
            </div>
            <a href="{{ route('admin.stores.index') }}" class="admin-button admin-button-ghost">
                <i data-lucide="arrow-left"></i>
                Kembali
            </a>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-5">
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
            <div class="admin-card admin-stat-card bg-emerald-700 text-white">
                <p class="admin-stat-label text-emerald-100">Sales</p>
                <h2 class="admin-stat-value text-white">Rp {{ number_format($stats['total_sales'], 0, ',', '.') }}</h2>
                <p class="admin-stat-meta text-emerald-100">{{ $stats['total_transactions'] }} transaksi</p>
            </div>
        </div>

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
                        <div class="admin-stat-label">Catatan Admin</div>
                        <p class="mt-2">{{ $store->catatan_admin ?: '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="admin-card p-6">
                <h2 class="text-2xl font-extrabold mb-5">Aksi Seller</h2>
                <div class="space-y-4">
                    @if($store->status === 'pending')
                        <form method="POST" action="{{ route('admin.stores.approve', $store->id) }}">
                            @csrf
                            <button class="admin-button admin-button-primary w-full" type="submit">Approve Seller</button>
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
                            <textarea class="admin-form-control" name="reason" placeholder="Alasan ban..." required></textarea>
                            <button class="admin-button admin-button-danger w-full" type="submit">Ban Seller</button>
                        </form>
                    @endif

                    @if(in_array($store->status, ['rejected', 'suspended', 'banned']))
                        <form method="POST" action="{{ route('admin.stores.activate', $store->id) }}">
                            @csrf
                            <button class="admin-button admin-button-primary w-full" type="submit">Aktifkan Kembali</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

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
            <div class="admin-card p-6">
                <h2 class="text-2xl font-extrabold mb-5">Laporan Toko & Produk</h2>
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
