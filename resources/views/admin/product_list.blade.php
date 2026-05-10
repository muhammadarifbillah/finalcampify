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

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @forelse($products as $product)
                @php
                    $image = $product->image ?: $product->gambar;
                    $imageUrl = $image
                        ? asset($image)
                        : null;
                @endphp
                <div class="admin-card overflow-hidden">
                    <div class="h-48 bg-slate-100">
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="admin-empty h-full">No Image</div>
                        @endif
                    </div>
                    <div class="space-y-4 p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="admin-badge admin-badge-muted">{{ $product->category ?? 'Umum' }}</span>
                                <h2 class="mt-3 text-xl font-extrabold">{{ $product->name }}</h2>
                            </div>
                            <span class="admin-badge {{ $statusBadge($product->status) }}">{{ $product->status }}</span>
                        </div>
                        <p class="text-sm text-slate-600">{{ \Illuminate\Support\Str::limit($product->description, 90) }}</p>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <div class="admin-stat-label">Harga Beli</div>
                                <p class="font-extrabold text-emerald-700">Rp {{ number_format($product->buy_price ?: $product->price, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <div class="admin-stat-label">Stock</div>
                                <p class="font-extrabold">{{ $product->stock }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-3 border-t border-slate-100 pt-4">
                            <div class="text-sm text-slate-500">{{ $product->store?->nama_toko ?? 'Tanpa toko' }}</div>
                            <a href="{{ route('admin.products.show', $product->id) }}" class="admin-button admin-button-primary">Detail</a>
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
