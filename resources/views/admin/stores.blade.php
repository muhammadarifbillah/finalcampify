@extends('layouts.admin')

@section('content')

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Manajemen Seller</h1>
            <p class="text-gray-600">Kelola status seller dan lakukan moderasi jika diperlukan.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-xl">
            <p class="text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    @if($stores->isEmpty())
        <div class="bg-white p-6 rounded-3xl shadow text-gray-500">Tidak ada seller yang terdaftar saat ini.</div>
    @else
        <div class="bg-white rounded-xl shadow overflow-x-auto">
            <div class="p-4 border-b">
                <h2 class="font-bold">Daftar Seller</h2>
            </div>
            <table class="w-full text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-4">Nama User</th>
                        <th>Nama Toko</th>
                        <th>Status</th>
                        <th>Produk</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stores as $store)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-4">
                                <div class="font-medium">{{ $store->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $store->user->email }}</div>
                            </td>
                            <td>
                                <div class="font-medium">{{ $store->nama_toko }}</div>
                                @if($store->alamat)
                                    <div class="text-sm text-gray-500">{{ Str::limit($store->alamat, 30) }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                                @if($store->status == 'active') bg-green-100 text-green-800
                                                @elseif($store->status == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($store->status == 'rejected') bg-red-100 text-red-800
                                                @elseif($store->status == 'suspended') bg-orange-100 text-orange-800
                                                @elseif($store->status == 'banned') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                    {{ ucfirst($store->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="text-sm">
                                    <div>{{ $store->products->count() }} produk</div>
                                    <div class="text-gray-500">{{ $store->products->where('status', 'approved')->count() }} aktif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('admin.stores.show', $store->id) }}"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

@endsection