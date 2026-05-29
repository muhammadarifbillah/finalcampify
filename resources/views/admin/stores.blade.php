@extends('layouts.admin')

@section('title', 'Stores Admin')

@php
    $statusBadge = fn ($status) => match ($status) {
        'active' => 'admin-badge-success',
        'pending' => 'admin-badge-warning',
        'rejected', 'banned' => 'admin-badge-danger',
        'suspended' => 'admin-badge-info',
        default => 'admin-badge-muted',
    };
@endphp

@section('content')
    <div class="space-y-8">
        <div>
            <h1 class="admin-section-title">Detail Toko</h1>
            <p class="admin-section-subtitle">Kelola seller, status toko, dan validasi produk yang dikirim penjual.</p>
        </div>

        <div class="grid gap-5 md:grid-cols-4">
            <div class="admin-card admin-stat-card">
                <p class="admin-stat-label">Total Seller</p>
                <h2 class="admin-stat-value">{{ number_format($stores->count()) }}</h2>
                <p class="admin-stat-meta">{{ $stores->where('status', 'active')->count() }} aktif</p>
            </div>
            <div class="admin-card admin-stat-card">
                <p class="admin-stat-label">Produk Waiting</p>
                <h2 class="admin-stat-value">{{ number_format($stores->sum('admin_waiting_products_count')) }}</h2>
                <p class="admin-stat-meta">Butuh review admin</p>
            </div>
            <div class="admin-card admin-stat-card">
                <p class="admin-stat-label">Produk Approved</p>
                <h2 class="admin-stat-value">{{ number_format($stores->sum('admin_approved_products_count')) }}</h2>
                <p class="admin-stat-meta">Tampil ke buyer</p>
            </div>
            <div class="admin-card admin-stat-card bg-red-700 text-white">
                <p class="admin-stat-label text-red-100">Seller Banned</p>
                <h2 class="admin-stat-value text-white">{{ $stores->where('status', 'banned')->count() }}</h2>
                <p class="admin-stat-meta text-red-100">Diblokir admin</p>
            </div>
        </div>

        <div class="admin-card">
            <div class="p-6">
                <h2 class="text-2xl font-extrabold">Daftar Seller</h2>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Pemilik</th>
                            <th>Nama Toko</th>
                            <th>Status</th>
                            <th>Produk</th>
                            <th>Retur</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stores as $store)
                            <tr>
                                <td>
                                    <div class="font-extrabold">{{ $store->user->name ?? '-' }}</div>
                                    <div class="text-xs text-slate-500">{{ $store->user->email ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="font-extrabold">{{ $store->nama_toko }}</div>
                                    <div class="text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($store->alamat, 42) }}</div>
                                </td>
                                <td><span class="admin-badge {{ $statusBadge($store->status) }}">{{ $store->status }}</span></td>
                                <td>
                                    <div class="font-extrabold">{{ $store->admin_products_count }} produk</div>
                                    <div class="text-xs text-slate-500">{{ $store->admin_approved_products_count }} approved, {{ $store->admin_waiting_products_count }} waiting</div>
                                </td>
                                <td>
                                    @if(($store->admin_return_count ?? 0) >= 3)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-1 text-xs font-bold text-red-700 ring-1 ring-red-400">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                            {{ $store->admin_return_count }}× ⚠
                                        </span>
                                    @elseif(($store->admin_return_count ?? 0) > 0)
                                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                            {{ $store->admin_return_count }}×
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.stores.show', $store->id) }}" class="admin-button admin-button-primary">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="admin-empty">Tidak ada seller yang terdaftar.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
