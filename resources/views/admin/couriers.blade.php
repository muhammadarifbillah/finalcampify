@extends('layouts.admin')

@section('title', 'Kurir Mitra')

@section('content')
<div class="space-y-8">
    <div>
        <h1 class="admin-section-title">Kurir Mitra</h1>
        <p class="admin-section-subtitle">Data layanan pengiriman dari mitra resmi. Admin hanya dapat melihat data ini.</p>
    </div>

    <div class="grid gap-6 md:grid-cols-3">
        {{-- Card 1: Total Kurir --}}
        <div class="bg-white border border-slate-200 hover:border-[#059669] hover:shadow-[0_12px_30px_-10px_rgba(5,150,105,0.2)] rounded-2xl p-6 shadow-sm transition-all duration-300 relative overflow-hidden flex flex-col justify-between group min-h-[140px]">
            <div class="absolute top-0 left-0 w-full h-[4px] bg-[#059669]"></div>
            <div class="flex justify-between items-start mb-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Kurir</span>
                <div class="w-8 h-8 rounded-lg bg-[#ecfdf5] text-[#059669] flex items-center justify-center transition-colors group-hover:bg-[#059669] group-hover:text-white">
                    <i data-lucide="truck" class="w-4 h-4"></i>
                </div>
            </div>
            <div>
                <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ number_format($couriers->count()) }}</h2>
                <p class="text-[11px] text-slate-400 font-bold mt-1">Layanan mitra tersedia</p>
            </div>
        </div>

        {{-- Card 2: Jenis Layanan --}}
        <div class="bg-white border border-slate-200 hover:border-[#0f766e] hover:shadow-[0_12px_30px_-10px_rgba(15,118,110,0.2)] rounded-2xl p-6 shadow-sm transition-all duration-300 relative overflow-hidden flex flex-col justify-between group min-h-[140px]">
            <div class="absolute top-0 left-0 w-full h-[4px] bg-[#0f766e]"></div>
            <div class="flex justify-between items-start mb-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Jenis Layanan</span>
                <div class="w-8 h-8 rounded-lg bg-[#f0fdfa] text-[#0f766e] flex items-center justify-center transition-colors group-hover:bg-[#0f766e] group-hover:text-white">
                    <i data-lucide="package" class="w-4 h-4"></i>
                </div>
            </div>
            <div>
                <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ number_format($serviceList->count()) }}</h2>
                <p class="text-[11px] text-slate-400 font-bold mt-1">Layanan unik tercatat</p>
            </div>
        </div>

        {{-- Card 3: Filter Aktif --}}
        <div class="bg-white border border-slate-200 hover:border-[#0369a1] hover:shadow-[0_12px_30px_-10px_rgba(3,105,161,0.2)] rounded-2xl p-6 shadow-sm transition-all duration-300 relative overflow-hidden flex flex-col justify-between group min-h-[140px]">
            <div class="absolute top-0 left-0 w-full h-[4px] bg-[#0369a1]"></div>
            <div class="flex justify-between items-start mb-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Filter Aktif</span>
                <div class="w-8 h-8 rounded-lg bg-[#f0f9ff] text-[#0369a1] flex items-center justify-center transition-colors group-hover:bg-[#0369a1] group-hover:text-white">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                </div>
            </div>
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight truncate">{{ $selectedService ?: 'Semua Layanan' }}</h2>
                <p class="text-[11px] text-slate-400 font-bold mt-1">Status pencarian aktif</p>
            </div>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <div class="flex flex-wrap gap-2">
            <a href="/admin/couriers"
                class="px-5 py-2 rounded-xl text-sm font-bold transition-all {{ !$selectedService ? 'bg-slate-800 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Semua</a>
            @foreach($serviceList as $service)
            <a href="/admin/couriers?service={{ urlencode($service) }}"
                class="px-5 py-2 rounded-xl text-sm font-bold transition-all {{ $selectedService === $service ? 'bg-slate-800 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">{{ $service }}</a>
            @endforeach
        </div>
    </div>

    {{-- Tabel Kurir (read-only) --}}
    <div>
        <div class="mb-4">
            <h2 class="text-2xl font-semibold text-slate-900">Daftar Kurir Mitra</h2>
            <p class="text-slate-500 text-sm mt-1">Data ini diambil langsung dari mitra resmi dan tidak dapat diubah secara manual oleh admin.</p>
        </div>

        <div class="admin-card rounded-xl border border-gray-100 overflow-hidden mt-4">
            <div class="overflow-x-auto">
                <table class="admin-table w-full text-sm">
                    <thead class="bg-[#f8fbf9] border-b border-gray-100 text-gray-500 text-xs tracking-wider">
                        <tr>
                            <th class="py-4 px-4 text-left font-bold">NAMA KURIR</th>
                            <th class="py-4 px-4 text-left font-bold">JENIS LAYANAN</th>
                            <th class="py-4 px-4 text-left font-bold">ESTIMASI (HARI)</th>
                            <th class="py-4 px-4 text-left font-bold">ONGKOS KIRIM</th>
                            <th class="py-4 px-4 text-center font-bold">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($couriers as $courier)
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-4">
                                <div class="font-extrabold text-slate-800 flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                                        <i data-lucide="truck" class="w-4 h-4"></i>
                                    </div>
                                    {{ $courier->name }}
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-block bg-slate-100 text-slate-700 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wide">
                                    {{ $courier->service }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-slate-600 font-medium">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                                    {{ $courier->estimate ?? '-' }}
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-emerald-600">Rp {{ number_format($courier->price, 0, ',', '.') }}</div>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="inline-block px-3 py-1 text-[10px] font-bold tracking-wider rounded-full {{ $courier->status === 'aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }} uppercase">
                                    {{ $courier->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-gray-400 text-sm">
                                <i data-lucide="package-x" style="width: 32px; height: 32px;" class="mx-auto mb-2 opacity-30"></i>
                                <p>Belum ada data kurir untuk filter ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
