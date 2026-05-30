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

        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 flex gap-4 items-start shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
            <div class="w-10 h-10 rounded-full bg-white border border-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                <i data-lucide="info" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="text-sm font-extrabold text-blue-900 tracking-tight">Pemberitahuan Sistem (Legacy View)</h3>
                <p class="text-xs text-blue-700 mt-1 font-medium leading-relaxed">Sistem validasi utama kini telah dipindahkan ke halaman <strong class="font-extrabold">Detail Toko</strong>. Anda tetap dapat memantau produk dengan status waiting, approved, dan rejected secara terpusat dari profil toko masing-masing seller.</p>
            </div>
        </div>

        <div class="admin-card rounded-xl border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-[#f8fbf9]">
                <h2 class="text-lg font-black text-slate-800 flex items-center gap-2">
                    <i data-lucide="clock" class="w-5 h-5 text-amber-500"></i> Antrean Validasi Produk
                </h2>
                <span class="inline-block px-3 py-1 text-[10px] font-bold tracking-wider rounded-full bg-amber-100 text-amber-700 uppercase">
                    {{ $products->count() }} Menunggu
                </span>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table w-full text-sm">
                    <thead class="bg-white border-b border-gray-100 text-gray-500 text-xs tracking-wider">
                        <tr>
                            <th class="py-4 px-4 text-left font-bold">PRODUK</th>
                            <th class="py-4 px-4 text-left font-bold">TOKO PENJUAL</th>
                            <th class="py-4 px-4 text-center font-bold">STATUS</th>
                            <th class="py-4 px-4 text-left font-bold">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="py-4 px-4 font-extrabold text-slate-800">{{ $product->name }}</td>
                                <td class="py-4 px-4 text-slate-600 font-medium">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="store" class="w-4 h-4 text-slate-400"></i>
                                        {{ $product->store?->nama_toko ?? '-' }}
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="inline-block px-2.5 py-1 text-[9px] font-bold tracking-wider rounded-full bg-amber-100 text-amber-700 uppercase animate-pulse">
                                        {{ $product->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.products.show', $product->id) }}" class="admin-button admin-button-ghost py-1.5 text-xs">Detail</a>
                                        <a href="/admin/products/approve/{{ $product->id }}" class="admin-button admin-button-primary bg-emerald-600 hover:bg-emerald-700 border-emerald-600 py-1.5 text-xs">Approve</a>
                                        <a href="/admin/products/reject/{{ $product->id }}" class="admin-button admin-button-danger py-1.5 text-xs">Reject</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-10 text-center text-gray-400 text-sm">
                                    <i data-lucide="check-circle" style="width: 32px; height: 32px;" class="mx-auto mb-2 text-emerald-400 opacity-50"></i>
                                    <p>Semua produk telah divalidasi. Tidak ada antrean.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
