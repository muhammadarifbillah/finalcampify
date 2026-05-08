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
            <a href="/admin/couriers"
                class="admin-button {{ !$selectedService ? 'admin-button-primary' : 'admin-button-ghost' }}">Semua</a>
            @foreach($serviceList as $service)
            <a href="/admin/couriers?service={{ urlencode($service) }}"
                class="admin-button {{ $selectedService === $service ? 'admin-button-primary' : 'admin-button-ghost' }}">{{ $service }}</a>
            @endforeach
        </div>
    </div>

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900">Kelola Kurir</h2>
            <p class="text-slate-500">Tambah, edit, atau atur layanan pengiriman tanpa mengubah logika yang sudah ada.
            </p>
        </div>
        <button id="openCourierModal"
            class="bg-emerald-600 text-white px-5 py-3 rounded-3xl font-semibold hover:bg-emerald-700 transition">Tambah
            Kurir</button>
    </div>

    <div id="courierModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40 p-4">
        <div class="w-full max-w-xl rounded-[32px] bg-white p-6 shadow-2xl max-h-[calc(100vh-3rem)] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 id="courierModalTitle" class="text-2xl font-semibold">Tambah Kurir</h2>
                <button type="button" id="closeCourierModal" class="text-slate-500 hover:text-slate-900">Tutup</button>
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
                            <td><span
                                    class="admin-badge {{ $courier->status === 'aktif' ? 'admin-badge-success' : 'admin-badge-muted' }}">{{ $courier->status }}</span>
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
    @endsection