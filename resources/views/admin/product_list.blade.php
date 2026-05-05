@extends('layouts.admin')

@section('content')

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Semua Produk</h1>
            <p class="text-gray-600">Tampilkan semua produk outdoor dalam katalog.</p>
        </div>
        <div class="text-sm text-gray-500">
            Total Produk: <span class="font-bold">{{ $products->count() }}</span>
        </div>
    </div>

    @if($products->isEmpty())
        <div class="bg-white p-6 rounded-xl shadow text-gray-500">Tidak ada produk dalam katalog saat ini.</div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
                    <!-- Gambar Produk -->
                    <div class="relative h-48 bg-gray-100 overflow-hidden">
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" 
                            class="w-full h-full object-cover hover:scale-105 transition duration-300">
                        
                        <!-- Status Badge -->
                        <div class="absolute top-3 left-3">
                            <span class="inline-flex rounded-lg px-3 py-1 text-xs font-semibold
                                @if($product->status == 'approved') bg-green-100 text-green-800
                                @elseif($product->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($product->status == 'rejected') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($product->status) }}
                            </span>
                        </div>

                        <!-- Rating Badge -->
                        @if($product->rating > 0)
                            <div class="absolute top-3 right-3 bg-yellow-400 text-white px-2 py-1 rounded-lg text-xs font-bold">
                                ⭐ {{ $product->rating }}
                            </div>
                        @endif
                    </div>

                    <!-- Konten Produk -->
                    <div class="p-4">
                        <!-- Kategori -->
                        <span class="inline-flex text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded mb-2">
                            {{ $product->category }}
                        </span>

                        <!-- Nama Produk -->
                        <h3 class="text-lg font-bold text-gray-900 mb-1 line-clamp-2">
                            {{ $product->name }}
                        </h3>

                        <!-- Deskripsi Singkat -->
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                            {{ Str::limit($product->description, 80) }}
                        </p>

                        <!-- Info Toko -->
                        @if($product->store)
                            <div class="mb-3 pb-3 border-b">
                                <p class="text-xs text-gray-500">Toko</p>
                                <p class="text-sm font-medium text-gray-900">{{ $product->store->nama_toko }}</p>
                            </div>
                        @endif

                        <!-- Harga -->
                        <div class="grid grid-cols-2 gap-2 mb-3">
                            <div>
                                <p class="text-xs text-gray-500">Harga Beli</p>
                                <p class="text-sm font-bold text-green-600">Rp {{ number_format($product->buy_price, 0, ',', '.') }}</p>
                            </div>
                            @if($product->rent_price > 0)
                                <div>
                                    <p class="text-xs text-gray-500">Harga Sewa</p>
                                    <p class="text-sm font-bold text-blue-600">Rp {{ number_format($product->rent_price, 0, ',', '.') }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Stok & Review -->
                        <div class="grid grid-cols-2 gap-2 mb-4 text-xs">
                            <div class="bg-blue-50 p-2 rounded">
                                <p class="text-gray-600">Stok</p>
                                <p class="font-bold text-blue-600">{{ $product->stock }}</p>
                            </div>
                            <div class="bg-purple-50 p-2 rounded">
                                <p class="text-gray-600">Review</p>
                                <p class="font-bold text-purple-600">{{ $product->reviews_count }}</p>
                            </div>
                        </div>

                        <!-- Tombol Detail -->
                        <a href="{{ route('admin.products.show', $product->id) }}"
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endsection