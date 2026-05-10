@extends('layouts.admin')

@section('title', 'Kurir Mitra')

@section('content')
<div class="space-y-8">
    <div>
        <h1 class="admin-section-title">Kurir Mitra</h1>
        <p class="admin-section-subtitle">Data layanan pengiriman dari mitra resmi. Admin hanya dapat melihat data ini.</p>
    </div>

    <div class="grid gap-5 md:grid-cols-3">
        <div class="admin-card admin-stat-card">
            <p class="admin-stat-label">Total Kurir</p>
            <h2 class="admin-stat-value">{{ $couriers->count() }}</h2>
            <p class="admin-stat-meta">Layanan tersedia</p>
        </div>
        <div class="admin-card admin-stat-card">
            <p class="admin-stat-label">Jenis Layanan</p>
            <h2 class="admin-stat-value">{{ $serviceList->count() }}</h2>
            <p class="admin-stat-meta">Layanan unik</p>
        </div>
        <div class="admin-card admin-stat-card">
            <p class="admin-stat-label">Filter Aktif</p>
            <h2 class="admin-stat-value text-2xl">{{ $selectedService ?: 'Semua' }}</h2>
            <p class="admin-stat-meta">Sedang ditampilkan</p>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="admin-card p-5">
        <div class="flex flex-wrap gap-2">
            <a href="/admin/couriers"
                class="admin-button {{ !$selectedService ? 'admin-button-primary' : 'admin-button-ghost' }}">Semua</a>
            @foreach($serviceList as $service)
            <a href="/admin/couriers?service={{ urlencode($service) }}"
                class="admin-button {{ $selectedService === $service ? 'admin-button-primary' : 'admin-button-ghost' }}">{{ $service }}</a>
            @endforeach
        </div>
    </div>

    {{-- Tabel Kurir (read-only) --}}
    <div>
        <div class="mb-4">
            <h2 class="text-2xl font-semibold text-slate-900">Daftar Kurir Mitra</h2>
            <p class="text-slate-500 text-sm mt-1">Data ini diambil langsung dari mitra resmi dan tidak dapat diubah secara manual oleh admin.</p>
        </div>

        <div class="admin-card p-6">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nama Kurir</th>
                            <th>Jenis Layanan</th>
                            <th>Estimasi</th>
                            <th>Ongkir</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($couriers as $courier)
                        <tr>
                            <td class="font-semibold text-slate-800">{{ $courier->name }}</td>
                            <td>
                                <span class="inline-block bg-slate-100 text-slate-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">
                                    {{ $courier->service }}
                                </span>
                            </td>
                            <td class="text-slate-600">{{ $courier->estimate ?? '-' }}</td>
                            <td class="font-bold text-emerald-600">Rp {{ number_format($courier->price, 0, ',', '.') }}</td>
                            <td>
                                <span class="admin-badge {{ $courier->status === 'aktif' ? 'admin-badge-success' : 'admin-badge-muted' }}">
                                    {{ strtoupper($courier->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="admin-empty">Belum ada data kurir.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
