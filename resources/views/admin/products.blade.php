@extends('layouts.admin')

@section('title', 'Produk Pending')

@section('content')
    <div class="space-y-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="admin-section-title">Validasi Produk</h1>
                <p class="admin-section-subtitle">Halaman legacy. Validasi utama sekarang ada di Detail Toko.</p>
            </div>
            <a href="{{ route('admin.stores.index') }}" class="admin-button admin-button-primary">
                <i data-lucide="store"></i>
                Ke Detail Toko
            </a>
        </div>

        <div class="admin-alert admin-alert-info">
            <i data-lucide="info"></i>
            <div>
                <p class="font-extrabold">Validasi pindah ke Detail Toko</p>
                <p>Produk waiting, approved, dan rejected tetap bisa dipantau dari halaman toko seller.</p>
            </div>
        </div>

        <div class="admin-card">
            <div class="p-6">
                <h2 class="text-2xl font-extrabold">Produk Waiting</h2>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Toko</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->store?->nama_toko ?? '-' }}</td>
                                <td><span class="admin-badge admin-badge-warning">{{ $product->status }}</span></td>
                                <td>
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.products.show', $product->id) }}" class="admin-button admin-button-ghost">Detail</a>
                                        <a href="/admin/products/approve/{{ $product->id }}" class="admin-button admin-button-primary">Approve</a>
                                        <a href="/admin/products/reject/{{ $product->id }}" class="admin-button admin-button-danger">Reject</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4"><div class="admin-empty">Tidak ada produk waiting.</div></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
