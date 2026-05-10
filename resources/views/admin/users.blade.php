@extends('layouts.admin')

@section('title', 'Users Management')

@php
    $badgeClass = fn ($status) => match ($status ?? 'active') {
        'active' => 'admin-badge-success',
        'inactive' => 'admin-badge-warning',
        'banned' => 'admin-badge-danger',
        default => 'admin-badge-muted',
    };
@endphp

@section('content')
    <div class="space-y-8">
        <div>
            <h1 class="admin-section-title">Users Management</h1>
            <p class="admin-section-subtitle">Daftar user, filter role, search, pagination, dan detail akun.</p>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
            <div class="admin-card admin-stat-card">
                <p class="admin-stat-label">Buyer</p>
                <h2 class="admin-stat-value">{{ number_format($roleCounts['buyer'] ?? 0) }}</h2>
                <p class="admin-stat-meta">Pengguna marketplace</p>
            </div>
            <div class="admin-card admin-stat-card">
                <p class="admin-stat-label">Seller</p>
                <h2 class="admin-stat-value">{{ number_format($roleCounts['seller'] ?? 0) }}</h2>
                <p class="admin-stat-meta">Pemilik toko</p>
            </div>
            <div class="admin-card admin-stat-card">
                <p class="admin-stat-label">Admin</p>
                <h2 class="admin-stat-value">{{ number_format($roleCounts['admin'] ?? 0) }}</h2>
                <p class="admin-stat-meta">Pengelola sistem</p>
            </div>
        </div>

        <div class="admin-card p-5">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid gap-4 lg:grid-cols-[1fr_180px_180px_auto]">
                <input class="admin-form-control" type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama atau email...">
                <select class="admin-form-control" name="role">
                    <option value="">Semua role</option>
                    @foreach(['admin', 'seller', 'buyer'] as $role)
                        <option value="{{ $role }}" @selected(request('role') === $role)>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
                <select class="admin-form-control" name="status">
                    <option value="">Semua status</option>
                    @foreach(['active', 'inactive', 'banned'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button class="admin-button admin-button-primary" type="submit">
                    <i data-lucide="filter"></i>
                    Filter
                </button>
            </form>
        </div>

        <div class="admin-card">
            <div class="flex flex-col gap-3 p-6 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-2xl font-extrabold">Daftar Pengguna</h2>
                <span class="text-sm font-bold text-slate-500">Total {{ number_format($users->total()) }} user</span>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th class="p-3">Nama</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>KYC Status</th>
                            <th>Last Login</th>
                            <th>Toko</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="admin-avatar">{{ strtoupper(substr($u->name ?? $u->email, 0, 1)) }}</div>
                                        <div>
                                            <div class="font-extrabold">{{ $u->name ?? '-' }}</div>
                                            <div class="text-xs text-slate-500">{{ $u->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="admin-badge admin-badge-muted">{{ $u->role ?? 'buyer' }}</span></td>
                                <td><span class="admin-badge {{ $badgeClass($u->status) }}">{{ $u->status ?? 'active' }}</span></td>
                                <td>
                                    @if($u->ktp_verified_at)
                                        <span class="admin-badge admin-badge-success">VERIFIED</span>
                                    @elseif($u->ktp_image)
                                        <span class="admin-badge admin-badge-warning animate-pulse">PENDING</span>
                                    @else
                                        <span class="admin-badge admin-badge-muted">NONE</span>
                                    @endif
                                </td>
                                <td>{{ $u->last_login ? \Carbon\Carbon::parse($u->last_login)->diffForHumans() : 'Belum pernah' }}</td>
                                <td>{{ $u->store?->nama_toko ?? '-' }}</td>
                                <td>
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.users.show', $u->id) }}" class="admin-button admin-button-ghost">Detail</a>
                                        @if($u->role !== 'admin')
                                            <a href="/admin/users/delete/{{ $u->id }}" onclick="return confirm('Yakin hapus user ini?')" class="admin-button admin-button-danger">Hapus</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="admin-empty">Tidak ada user sesuai filter.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
