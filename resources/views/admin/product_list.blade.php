@extends('layouts.admin')

@section('title', 'Products Admin')

@php
    $statusBadge = fn ($status) => match ($status) {
        'approved' => 'admin-badge-success',
        'waiting', 'pending' => 'admin-badge-warning',
        'rejected' => 'admin-badge-danger',
        default => 'admin-badge-muted',
    };
@endphp

@section('content')
    <div class="space-y-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="admin-section-title">Products</h1>
                <p class="admin-section-subtitle">Katalog produk seller lintas status validasi.</p>
            </div>
            <div class="admin-badge admin-badge-muted">Total {{ number_format($products->count()) }}</div>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
            <div class="admin-card admin-stat-card">
                <p class="admin-stat-label">Approved</p>
                <h2 class="admin-stat-value">{{ $products->where('status', 'approved')->count() }}</h2>
                <p class="admin-stat-meta">Tampil ke buyer</p>
            </div>
            <div class="admin-card admin-stat-card">
                <p class="admin-stat-label">Waiting</p>
                <h2 class="admin-stat-value">{{ $products->whereIn('status', ['waiting', 'pending'])->count() }}</h2>
                <p class="admin-stat-meta">Menunggu admin</p>
            </div>
            <div class="admin-card admin-stat-card">
                <p class="admin-stat-label">Rejected</p>
                <h2 class="admin-stat-value">{{ $products->where('status', 'rejected')->count() }}</h2>
                <p class="admin-stat-meta">Ditolak review</p>
            </div>
            <div class="admin-card admin-stat-card bg-emerald-700 text-white">
                <p class="admin-stat-label text-emerald-100">Stock</p>
                <h2 class="admin-stat-value text-white">{{ number_format($products->sum('stock')) }}</h2>
                <p class="admin-stat-meta text-emerald-100">Total unit katalog</p>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2 2xl:grid-cols-3">
            @forelse($products as $product)
                 @php
                     $imageUrl = $product->image_url ?: null;
                 @endphp
                  <div class="admin-card overflow-hidden h-full flex flex-col">
                    <div class="w-full h-60 bg-white border-b border-slate-100 relative group overflow-hidden">
                        @if($imageUrl)
                            <div class="absolute inset-4 flex items-center justify-center">
                                <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="max-w-full max-h-full object-contain transition-transform duration-300 group-hover:scale-105">
                            </div>
                        @else
                            <div class="absolute inset-4 flex items-center justify-center bg-slate-50 text-slate-400 rounded-lg">
                                <i data-lucide="image" class="w-8 h-8 opacity-45"></i>
                            </div>
                        @endif
                    </div>
                    <!-- Detail Produk dengan Penyeimbang Tinggi Otomatis -->
                    <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                        <div class="space-y-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <span class="admin-badge admin-badge-muted">{{ $product->category ?? 'Umum' }}</span>
                                    <h2 class="mt-2 text-lg font-extrabold text-slate-800 line-clamp-1" title="{{ $product->name }}">{{ $product->name }}</h2>
                                </div>
                                <span class="admin-badge {{ $statusBadge($product->status) }} shrink-0">{{ $product->status }}</span>
                            </div>
                            <p class="text-xs text-slate-600 line-clamp-2" style="min-height: 2rem;">{{ \Illuminate\Support\Str::limit($product->description, 90) }}</p>
                            <div class="grid grid-cols-2 gap-3 text-sm pt-1">
                                <div>
                                    <div class="admin-stat-label text-[10px] uppercase tracking-wider text-slate-400">Harga Beli</div>
                                    <p class="font-black text-emerald-700">Rp {{ number_format($product->buy_price ?: $product->price, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <div class="admin-stat-label text-[10px] uppercase tracking-wider text-slate-400">Stock</div>
                                    <p class="font-black text-slate-700">{{ $product->stock }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-3 border-t border-slate-100 pt-4 mt-auto">
                            <div class="text-xs font-semibold text-slate-500 truncate max-w-[150px]" title="{{ $product->store?->nama_toko ?? 'Tanpa toko' }}">
                                <i data-lucide="store" class="inline-block w-3 h-3 me-1 align-middle"></i>{{ $product->store?->nama_toko ?? 'Tanpa toko' }}
                            </div>
                            <a href="{{ route('admin.products.show', $product->id) }}" class="admin-button admin-button-primary py-1.5 text-xs">Detail</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="admin-card md:col-span-2 xl:col-span-3">
                    <div class="admin-empty">Tidak ada produk dalam katalog.</div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
