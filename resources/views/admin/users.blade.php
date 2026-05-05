@extends('layouts.admin')

@section('content')

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Manajemen User</h1>
            <p class="text-gray-600">Kelola pengguna sistem dan lakukan tindakan jika diperlukan.</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-3xl shadow">
        <h2 class="text-lg font-semibold mb-4">Daftar Pengguna</h2>
        @if($users->isEmpty())
            <p class="text-gray-500">Belum ada pengguna yang terdaftar.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3">Nama</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $u)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3">{{ $u->name }}</td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    <span
                                        class="inline-flex rounded-full 
                                                    @if($u->status == 'active') bg-green-100 text-green-800
                                                    @elseif($u->status == 'inactive') bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800
                                                    @endif px-3 py-1 text-xs font-semibold">{{ ucfirst($u->status ?? 'active') }}</span>
                                </td>
                                <td>{{ $u->last_login ? $u->last_login->diffForHumans() : 'Belum pernah' }}</td>
                                <td>
                                    <a href="/admin/users/{{ $u->id }}"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm mr-2">Detail</a>
                                    @if($u->role != 'admin')
                                        <a href="/admin/users/delete/{{ $u->id }}" onclick="return confirm('Yakin hapus user ini?')"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Hapus</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@endsection