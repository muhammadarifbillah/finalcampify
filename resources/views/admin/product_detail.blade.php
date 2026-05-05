@extends('layouts.admin')

@section('content')

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Detail Produk</h1>
            <p class="text-gray-600">Informasi lengkap produk outdoor.</p>
        </div>
        <a href="{{ route('admin.products.list') }}"
            class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition">
            ← Kembali ke Katalog
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Utama (Gambar & Info) -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Gambar Produk -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="h-96 bg-gray-100 flex items-center justify-center">
                    <img src="{{ $product->image }}" alt="{{ $product->name }}" 
                        class="w-full h-full object-cover">
                </div>
            </div>

            <!-- Informasi Dasar -->
            <div class="bg-white rounded-xl shadow p-6">
                <div class="space-y-4">
                    <!-- Status -->
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h2>
                            <p class="text-gray-600 mt-1">{{ $product->category }}</p>
                        </div>
                        <span class="inline-flex rounded-lg px-4 py-2 text-sm font-semibold
                            @if($product->status == 'approved') bg-green-100 text-green-800
                            @elseif($product->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($product->status == 'rejected') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($product->status) }}
                        </span>
                    </div>

                    <!-- Deskripsi -->
                    <div class="pt-4 border-t">
                        <h3 class="font-semibold text-gray-900 mb-2">Deskripsi Produk</h3>
                        <p class="text-gray-600 leading-relaxed">{{ $product->description }}</p>
                    </div>
                </div>
            </div>

            <!-- Harga & Stok -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-green-50 rounded-xl p-6 border border-green-200">
                    <p class="text-sm font-medium text-green-700 mb-1">💰 Harga Beli</p>
                    <p class="text-3xl font-bold text-green-600">Rp {{ number_format($product->buy_price, 0, ',', '.') }}</p>
                </div>
                
                @if($product->rent_price > 0)
                    <div class="bg-blue-50 rounded-xl p-6 border border-blue-200">
                        <p class="text-sm font-medium text-blue-700 mb-1">📌 Harga Sewa</p>
                        <p class="text-3xl font-bold text-blue-600">Rp {{ number_format($product->rent_price, 0, ',', '.') }}</p>
                    </div>
                @endif
            </div>

            <!-- Info Toko -->
            @if($product->store)
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-bold mb-4">🏪 Informasi Toko</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Nama Toko</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $product->store->nama_toko }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Pemilik</p>
                            <p class="text-gray-900">{{ $product->store->user->name }}</p>
                        </div>
                        @if($product->store->alamat)
                            <div>
                                <p class="text-sm text-gray-600">Alamat</p>
                                <p class="text-gray-900">{{ $product->store->alamat }}</p>
                            </div>
                        @endif
                        <a href="{{ route('admin.stores.show', $product->store->id) }}"
                            class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium mt-3">
                            Lihat Detail Toko →
                        </a>
                    </div>
                </div>
            @endif

            <!-- Kurir Terkait -->
            @if($product->couriers->count() > 0)
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="text-lg font-bold mb-4">🚚 Kurir yang Tersedia</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($product->couriers as $courier)
                            <div class="border rounded-lg p-3">
                                <p class="font-medium text-gray-900">{{ $courier->name }}</p>
                                <p class="text-sm text-gray-600">{{ $courier->service }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <p class="text-yellow-800">⚠️ Belum terhubung dengan kurir apapun</p>
                </div>
            @endif

        </div>

        <!-- Sidebar (Statistik & Aksi) -->
        <div class="space-y-6">

            <!-- Statistik -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-bold mb-4">📊 Statistik</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">⭐ Rating</span>
                        <span class="text-xl font-bold text-yellow-500">{{ $product->rating > 0 ? $product->rating : '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">💬 Review</span>
                        <span class="text-xl font-bold text-purple-600">{{ $product->reviews_count }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">📦 Stok</span>
                        <span class="text-xl font-bold 
                            @if($product->stock > 20) text-green-600
                            @elseif($product->stock > 5) text-yellow-600
                            @else text-red-600
                            @endif">{{ $product->stock }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">🏷️ Status Rental</span>
                        <span class="text-sm font-bold">
                            @if($product->is_rental) 
                                <span class="text-blue-600">✅ Ya</span>
                            @else 
                                <span class="text-gray-600">❌ Tidak</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Informasi Tanggal -->
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-bold mb-4">📅 Informasi Waktu</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-600">Dibuat</p>
                        <p class="font-medium text-gray-900">{{ $product->created_at->format('d M Y H:i') }}</p>
                        <p class="text-xs text-gray-500">{{ $product->created_at->diffForHumans() }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Diupdate</p>
                        <p class="font-medium text-gray-900">{{ $product->updated_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Aksi -->
            @if($product->status == 'pending')
                <div class="bg-blue-50 rounded-xl shadow p-6 border border-blue-200">
                    <h3 class="text-lg font-bold mb-4 text-blue-900">⚡ Aksi Admin</h3>
                    <div class="space-y-2">
                        <a href="/admin/products/approve/{{ $product->id }}"
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                            ✅ Approve
                        </a>
                        <a href="/admin/products/reject/{{ $product->id }}"
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                            ❌ Reject
                        </a>
                    </div>
                </div>
            @endif

        </div>

    </div>

@endsection