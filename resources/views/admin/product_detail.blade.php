@extends('layouts.admin')

@section('title', 'Detail Produk')

@php
    $statusBadge = fn ($status) => match ($status) {
        'approved' => 'admin-badge-success',
        'waiting', 'pending' => 'admin-badge-warning',
        'rejected' => 'admin-badge-danger',
        default => 'admin-badge-muted',
    };
    $image = $product->image ?: $product->gambar;
    $imageUrl = $image ? (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://']) ? $image : asset('storage/'.$image)) : null;
@endphp

@section('content')
    <div class="space-y-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="admin-section-title">{{ $product->name }}</h1>
                <p class="admin-section-subtitle">{{ $product->category ?? 'Tanpa kategori' }}</p>
            </div>
            <a href="{{ route('admin.products.list') }}" class="admin-button admin-button-ghost">
                <i data-lucide="arrow-left"></i>
                Kembali
            </a>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.35fr_.65fr]">
            <div class="space-y-6">
                <div class="admin-card overflow-hidden">
                    <div class="h-[420px] bg-slate-100">
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="admin-empty h-full">No Image</div>
                        @endif
                    </div>
                </div>

                <div class="admin-card p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-extrabold">Informasi Produk</h2>
                            <p class="mt-3 text-slate-600">{{ $product->description ?: 'Tidak ada deskripsi.' }}</p>
                        </div>
                        <span class="admin-badge {{ $statusBadge($product->status) }}">{{ $product->status }}</span>
                    </div>
                </div>

                @if($product->store)
                    <div class="admin-card p-6">
                        <h2 class="text-2xl font-extrabold mb-5">Informasi Toko</h2>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div><div class="admin-stat-label">Nama Toko</div><p class="mt-2 font-bold">{{ $product->store->nama_toko }}</p></div>
                            <div><div class="admin-stat-label">Pemilik</div><p class="mt-2 font-bold">{{ $product->store->user->name ?? '-' }}</p></div>
                            <div class="md:col-span-2"><div class="admin-stat-label">Alamat</div><p class="mt-2">{{ $product->store->alamat ?: '-' }}</p></div>
                        </div>
                        <a href="{{ route('admin.stores.show', $product->store->id) }}" class="admin-button admin-button-primary mt-5">Detail Toko</a>
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="admin-card p-6">
                    <h2 class="text-2xl font-extrabold mb-5">Statistik</h2>
                    <div class="space-y-4">
                        <div><div class="admin-stat-label">Harga Beli</div><p class="mt-2 text-2xl font-extrabold">Rp {{ number_format($product->buy_price ?: $product->price, 0, ',', '.') }}</p></div>
                        <div><div class="admin-stat-label">Harga Sewa</div><p class="mt-2 text-2xl font-extrabold">Rp {{ number_format($product->rent_price, 0, ',', '.') }}</p></div>
                        <div><div class="admin-stat-label">Stok</div><p class="mt-2 text-2xl font-extrabold">{{ $product->stock }}</p></div>
                        <div><div class="admin-stat-label">Rating</div><p class="mt-2 text-2xl font-extrabold">{{ $product->rating ?: '-' }}</p></div>
                        <div><div class="admin-stat-label">Review</div><p class="mt-2 text-2xl font-extrabold">{{ $product->reviews_count }}</p></div>
                    </div>
                </div>

                <div class="admin-card p-6">
                    <h2 class="text-2xl font-extrabold mb-5">Review Admin</h2>
                    @if($product->flag_reason)
                        <div class="admin-alert admin-alert-warning mb-4">
                            <i data-lucide="triangle-alert"></i>
                            <p>{{ $product->flag_reason }}</p>
                        </div>
                    @endif
                    @if(in_array($product->status, ['waiting', 'pending', 'rejected']))
                        <a href="/admin/products/approve/{{ $product->id }}" class="admin-button admin-button-primary w-full mb-3">Approve</a>
                    @endif
                    @if(in_array($product->status, ['waiting', 'pending', 'approved']))
                        <a href="/admin/products/reject/{{ $product->id }}" class="admin-button admin-button-danger w-full">Reject</a>
                    @endif
                    @if($product->reviewed_at)
                        <p class="mt-4 text-sm text-slate-500">Reviewed {{ $product->reviewed_at }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
