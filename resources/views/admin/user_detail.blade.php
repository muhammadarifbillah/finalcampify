@extends('layouts.admin')

@section('title', 'Detail User')

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
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="admin-section-title">{{ $user->name }}</h1>
                <p class="admin-section-subtitle">{{ $user->email }}</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="admin-button admin-button-ghost">
                <i data-lucide="arrow-left"></i>
                Kembali
            </a>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.3fr_.7fr]">
            <div class="admin-card p-6">
                <div class="flex items-start justify-between">
                    <h2 class="text-2xl font-extrabold">Informasi Akun</h2>
                    <span class="admin-badge {{ $badgeClass($user->status) }}">{{ $user->status ?? 'active' }}</span>
                </div>
                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    <div><div class="admin-stat-label">Nama Lengkap</div><p class="mt-2 font-bold">{{ $user->name }}</p></div>
                    <div><div class="admin-stat-label">Email</div><p class="mt-2 font-bold">{{ $user->email }}</p></div>
                    <div><div class="admin-stat-label">Role</div><p class="mt-2 font-bold">{{ ucfirst($user->role ?? 'buyer') }}</p></div>
                    <div><div class="admin-stat-label">Tanggal Daftar</div><p class="mt-2 font-bold">{{ $user->created_at?->format('d M Y H:i') ?? '-' }}</p></div>
                    <div><div class="admin-stat-label">Terakhir Login</div><p class="mt-2 font-bold">{{ $user->last_login?->format('d M Y H:i') ?? 'Belum pernah' }}</p></div>
                    <div><div class="admin-stat-label">Toko</div><p class="mt-2 font-bold">{{ $user->store?->nama_toko ?? '-' }}</p></div>
                </div>
            </div>

            <div class="admin-card p-6">
                <h2 class="text-2xl font-extrabold mb-5">Aksi Admin</h2>
                <div class="space-y-3">
                    @if($user->status !== 'active')
                        <a href="/admin/users/{{ $user->id }}/activate" class="admin-button admin-button-primary w-full">Aktifkan User</a>
                    @endif
                    @if($user->status !== 'inactive')
                        <a href="/admin/users/{{ $user->id }}/deactivate" class="admin-button admin-button-ghost w-full">Nonaktifkan User</a>
                    @endif
                    @if($user->status !== 'banned')
                        <a href="/admin/users/{{ $user->id }}/ban" class="admin-button admin-button-danger w-full">Ban User</a>
                    @endif
                    @if($user->role !== 'admin')
                        <a href="/admin/users/delete/{{ $user->id }}" onclick="return confirm('Yakin hapus user ini?')" class="admin-button admin-button-danger w-full">Hapus User</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="admin-card">
                <div class="p-6">
                    <h2 class="text-2xl font-extrabold">Riwayat Transaksi</h2>
                </div>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Total</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ optional($transaction->product)->name ?? 'Product #'.$transaction->product_id }}</td>
                                    <td>Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                                    <td>{{ $transaction->created_at?->format('d M Y H:i') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3"><div class="admin-empty">Belum ada transaksi.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="admin-card p-6">
                <h2 class="text-2xl font-extrabold mb-5">Riwayat Login</h2>
                @if(empty($loginActivities) || !$user->last_login)
                    <div class="admin-empty">Belum ada aktivitas login tercatat.</div>
                @else
                    <div class="space-y-3">
                        @foreach($loginActivities as $activity)
                            @if($activity['timestamp'])
                                <div class="rounded-lg bg-slate-50 p-4">
                                    <p class="font-bold">{{ ucfirst($activity['action']) }}</p>
                                    <p class="text-sm text-slate-500">{{ $activity['timestamp']->format('d M Y H:i') }} - IP: {{ $activity['ip'] }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
