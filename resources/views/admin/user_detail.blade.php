@extends('layouts.admin')

@section('content')

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Detail User: {{ $user->name }}</h1>
            <p class="text-gray-600">Kelola detail dan aktivitas pengguna.</p>
        </div>
        <a href="/admin/users" class="bg-gray-500 text-white px-5 py-3 rounded-xl font-semibold hover:bg-gray-600">←
            Kembali</a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 border border-green-200 p-4 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <!-- DATA LENGKAP USER -->
    <div class="bg-white p-6 rounded-3xl shadow mb-6">
        <h2 class="text-lg font-semibold mb-4">Informasi Akun</h2>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <p class="text-sm text-gray-500">Nama Lengkap</p>
                <p class="text-lg font-semibold">{{ $user->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Email</p>
                <p class="text-lg font-semibold">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Role</p>
                <p class="text-lg font-semibold">{{ ucfirst($user->role ?? 'user') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Status Akun</p>
                <span class="inline-flex rounded-full 
                        @if($user->status == 'active') bg-green-100 text-green-800
                        @elseif($user->status == 'inactive') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800
                        @endif px-3 py-1 text-sm font-semibold">{{ ucfirst($user->status ?? 'active') }}</span>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tanggal Daftar</p>
                <p class="text-lg font-semibold">{{ $user->created_at ? $user->created_at->format('d M Y H:i') : '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Terakhir Login</p>
                <p class="text-lg font-semibold">
                    {{ $user->last_login ? $user->last_login->format('d M Y H:i') : 'Belum pernah' }}</p>
            </div>
        </div>

        @if($user->store)
            <div class="mt-4 pt-4 border-t">
                <p class="text-sm text-gray-500">Toko</p>
                <p class="text-lg font-semibold">{{ $user->store->nama_toko }}</p>
            </div>
        @endif
    </div>

    <!-- AKSI ADMIN -->
    <div class="bg-white p-6 rounded-3xl shadow mb-6">
        <h2 class="text-lg font-semibold mb-4">Aksi Admin</h2>
        <div class="flex flex-wrap gap-3">
            @if($user->status != 'active')
                <a href="/admin/users/{{ $user->id }}/activate"
                    class="bg-green-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-green-700">Aktifkan User</a>
            @endif
            @if($user->status != 'inactive')
                <a href="/admin/users/{{ $user->id }}/deactivate"
                    class="bg-yellow-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-yellow-700">Nonaktifkan User</a>
            @endif
            @if($user->status != 'banned')
                <a href="/admin/users/{{ $user->id }}/ban"
                    class="bg-red-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-red-700">Ban User</a>
            @endif
            @if($user->role != 'admin')
                <a href="/admin/users/delete/{{ $user->id }}" onclick="return confirm('Yakin hapus user ini?')"
                    class="bg-gray-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-gray-700">Hapus User</a>
            @endif
        </div>
    </div>

    <!-- AKTIVITAS USER -->
    <div class="bg-white p-6 rounded-3xl shadow mb-6">
        <h2 class="text-lg font-semibold mb-4">Aktivitas User</h2>

        <!-- TRANSAKSI -->
        <div class="mb-6">
            <h3 class="text-md font-semibold mb-3">Riwayat Transaksi</h3>
            @if($transactions->isEmpty())
                <p class="text-gray-500">Belum ada transaksi.</p>
            @else
                <div class="space-y-2">
                    @foreach($transactions as $t)
                        <div class="bg-gray-50 p-3 rounded-xl">
                            <p class="font-semibold">{{ optional($t->product)->name ?? 'Product #' . $t->product_id }}</p>
                            <p class="text-sm text-gray-600">Rp {{ number_format($t->total, 0, ',', '.') }} -
                                {{ $t->created_at ? $t->created_at->format('d M Y H:i') : '-' }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- LOGIN ACTIVITIES -->
        <div>
            <h3 class="text-md font-semibold mb-3">Riwayat Login</h3>
            @if(empty($loginActivities) || !$user->last_login)
                <p class="text-gray-500">Belum ada aktivitas login tercatat.</p>
            @else
                <div class="space-y-2">
                    @foreach($loginActivities as $activity)
                        @if($activity['timestamp'])
                            <div class="bg-gray-50 p-3 rounded-xl">
                                <p class="font-semibold">{{ ucfirst($activity['action']) }}</p>
                                <p class="text-sm text-gray-600">{{ $activity['timestamp']->format('d M Y H:i') }} - IP:
                                    {{ $activity['ip'] }}</p>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>

@endsection