@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')
    <div class="space-y-8">
        <div>
            <h1 class="admin-section-title">Pengaturan</h1>
            <p class="admin-section-subtitle">Konfigurasi tampilan admin dan preferensi dasar sistem.</p>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="admin-card p-6">
                <h2 class="text-2xl font-extrabold mb-5">Profil Admin</h2>
                <div class="space-y-4">
                    <div>
                        <div class="admin-stat-label">Nama</div>
                        <p class="mt-2">{{ auth()->user()->name ?? '-' }}</p>
                    </div>
                    <div>
                        <div class="admin-stat-label">Email</div>
                        <p class="mt-2">{{ auth()->user()->email ?? '-' }}</p>
                    </div>
                    <div>
                        <div class="admin-stat-label">Role</div>
                        <p class="mt-2">{{ auth()->user()->role ?? 'admin' }}</p>
                    </div>
                </div>
            </div>

            <div class="admin-card p-6">
                <h2 class="text-2xl font-extrabold mb-5">Status Sistem</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="font-bold">Sidebar baru</span>
                        <span class="admin-badge admin-badge-success">Aktif</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-bold">Dashboard realtime</span>
                        <span class="admin-badge admin-badge-success">Aktif</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-bold">Validasi produk</span>
                        <span class="admin-badge admin-badge-info">Detail Toko</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
