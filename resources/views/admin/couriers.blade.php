@extends('layouts.admin')

@section('title', 'Courier Admin')

@section('content')
    <div class="space-y-8">
        <div>
            <h1 class="admin-section-title">Courier</h1>
            <p class="admin-section-subtitle">Data layanan pengiriman untuk pairing produk.</p>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
            <div class="admin-card admin-stat-card">
                <p class="admin-stat-label">Total Kurir</p>
                <h2 class="admin-stat-value">{{ $couriers->count() }}</h2>
                <p class="admin-stat-meta">Layanan tersedia</p>
            </div>
            <div class="admin-card admin-stat-card">
                <p class="admin-stat-label">Kategori</p>
                <h2 class="admin-stat-value">{{ $serviceList->count() }}</h2>
                <p class="admin-stat-meta">Service unik</p>
            </div>
            <div class="admin-card admin-stat-card">
                <p class="admin-stat-label">Filter</p>
                <h2 class="admin-stat-value text-2xl">{{ $selectedService ?: 'Semua' }}</h2>
                <p class="admin-stat-meta">Filter aktif</p>
            </div>
        </div>

        <div class="admin-card p-5">
            <div class="flex flex-wrap gap-2">
                <a href="/admin/couriers" class="admin-button {{ !$selectedService ? 'admin-button-primary' : 'admin-button-ghost' }}">Semua</a>
                @foreach($serviceList as $service)
                    <a href="/admin/couriers?service={{ urlencode($service) }}" class="admin-button {{ $selectedService === $service ? 'admin-button-primary' : 'admin-button-ghost' }}">{{ $service }}</a>
                @endforeach
            </div>
        </div>

        <div class="admin-card">
            <div class="p-6">
                <h2 class="text-2xl font-extrabold">Daftar Kurir</h2>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Layanan</th>
                            <th>Estimasi</th>
                            <th>Ongkir</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($couriers as $courier)
                            <tr>
                                <td>{{ $courier->name }}</td>
                                <td>{{ $courier->service }}</td>
                                <td>{{ $courier->estimate ?? '-' }}</td>
                                <td>Rp {{ number_format($courier->price, 0, ',', '.') }}</td>
                                <td><span class="admin-badge {{ $courier->status === 'aktif' ? 'admin-badge-success' : 'admin-badge-muted' }}">{{ $courier->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5"><div class="admin-empty">Belum ada data kurir.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
