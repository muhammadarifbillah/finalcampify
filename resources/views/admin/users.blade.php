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

        <div class="grid gap-6 md:grid-cols-3">
            {{-- Card 1: Buyer --}}
            <div class="bg-white border border-slate-200 hover:border-[#059669] hover:shadow-[0_12px_30px_-10px_rgba(5,150,105,0.2)] rounded-2xl p-6 shadow-sm transition-all duration-300 relative overflow-hidden flex flex-col justify-between group min-h-[140px]">
                <div class="absolute top-0 left-0 w-full h-[4px] bg-[#059669]"></div>
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Buyer</span>
                    <div class="w-8 h-8 rounded-lg bg-[#ecfdf5] text-[#059669] flex items-center justify-center transition-colors group-hover:bg-[#059669] group-hover:text-white">
                        <i data-lucide="users" class="w-4 h-4"></i>
                    </div>
                </div>
                <div>
                    <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ number_format($roleCounts['buyer'] ?? 0) }}</h2>
                    <p class="text-[11px] text-slate-400 font-bold mt-1">Pengguna marketplace</p>
                </div>
            </div>

            {{-- Card 2: Seller --}}
            <div class="bg-white border border-slate-200 hover:border-[#064e3b] hover:shadow-[0_12px_30px_-10px_rgba(6,78,59,0.2)] rounded-2xl p-6 shadow-sm transition-all duration-300 relative overflow-hidden flex flex-col justify-between group min-h-[140px]">
                <div class="absolute top-0 left-0 w-full h-[4px] bg-[#064e3b]"></div>
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Seller</span>
                    <div class="w-8 h-8 rounded-lg bg-[#e6f4ea] text-[#047857] flex items-center justify-center transition-colors group-hover:bg-[#064e3b] group-hover:text-white">
                        <i data-lucide="store" class="w-4 h-4"></i>
                    </div>
                </div>
                <div>
                    <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ number_format($roleCounts['seller'] ?? 0) }}</h2>
                    <p class="text-[11px] text-slate-400 font-bold mt-1">Pemilik toko</p>
                </div>
            </div>

            {{-- Card 3: Admin --}}
            <div class="bg-white border border-slate-200 hover:border-[#10b981] hover:shadow-[0_12px_30px_-10px_rgba(16,185,129,0.2)] rounded-2xl p-6 shadow-sm transition-all duration-300 relative overflow-hidden flex flex-col justify-between group min-h-[140px]">
                <div class="absolute top-0 left-0 w-full h-[4px] bg-[#10b981]"></div>
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Admin</span>
                    <div class="w-8 h-8 rounded-lg bg-[#f0fdf4] text-[#10b981] flex items-center justify-center transition-colors group-hover:bg-[#10b981] group-hover:text-white">
                        <i data-lucide="user-cog" class="w-4 h-4"></i>
                    </div>
                </div>
                <div>
                    <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ number_format($roleCounts['admin'] ?? 0) }}</h2>
                    <p class="text-[11px] text-slate-400 font-bold mt-1">Pengelola sistem</p>
                </div>
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
