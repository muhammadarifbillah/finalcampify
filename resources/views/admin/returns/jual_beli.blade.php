@extends('layouts.admin')

@section('title', 'Retur Jual-Beli')

@section('content')
@php
    $badgeMap = [
        'pending' => 'admin-badge-warning',
        'dispute' => 'admin-badge-danger',
        'checking' => 'admin-badge-info',
        'completed' => 'admin-badge-success',
        'rejected' => 'admin-badge-muted',
    ];
@endphp

<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="admin-section-title text-2xl font-bold">Retur Jual-Beli</h1>
            <p class="admin-section-subtitle text-gray-500">Kelola permintaan pengembalian dana dan barang dari transaksi marketplace.</p>
        </div>
        <button class="admin-button admin-button-primary bg-[#0f6b52] hover:bg-[#0c5843] text-white flex items-center gap-2">
            <i data-lucide="download" style="width: 16px; height: 16px;"></i> Export Laporan
        </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="admin-card p-5 border border-green-100 shadow-sm flex flex-col justify-between rounded-xl">
            <div class="text-[10px] font-bold text-gray-400 tracking-wider uppercase mb-3">Total Permintaan</div>
            <div class="flex items-end gap-2">
                <span class="text-4xl font-extrabold text-gray-800">{{ number_format($totalPermintaan, 0, ',', '.') }}</span>
                <span class="text-sm font-bold text-[#0f6b52] mb-1 flex items-center"><i data-lucide="trending-up" style="width:16px; height:16px;" class="mr-1"></i>12%</span>
            </div>
        </div>
        <div class="admin-card p-5 border border-red-100 shadow-sm flex flex-col justify-between rounded-xl">
            <div class="text-[10px] font-bold text-gray-400 tracking-wider uppercase mb-3">Butuh Mediasi</div>
            <div class="flex items-end justify-between">
                <span class="text-4xl font-extrabold text-red-500">{{ number_format($butuhMediasi, 0, ',', '.') }}</span>
                <span class="text-[10px] font-bold bg-red-100 text-red-500 px-2.5 py-1 rounded-md uppercase tracking-wider mb-1">Urgent</span>
            </div>
        </div>
        <div class="admin-card p-5 border border-green-100 shadow-sm flex flex-col justify-between rounded-xl">
            <div class="text-[10px] font-bold text-gray-400 tracking-wider uppercase mb-3">Escrow Tertahan</div>
            <div class="flex items-end gap-2">
                <span class="text-4xl font-extrabold text-gray-800">Rp{{ number_format($escrowTertahan / 1000000, 0, ',', '.') }}M</span>
                <span class="text-[11px] text-gray-500 mb-1 leading-tight flex flex-col"><span>Active</span><span>Pool</span></span>
            </div>
        </div>
        <div class="admin-card p-5 border border-green-100 shadow-sm flex flex-col justify-between rounded-xl">
            <div class="text-[10px] font-bold text-gray-400 tracking-wider uppercase mb-3">Avg. Resolusi</div>
            <div class="flex items-end justify-between">
                <span class="text-4xl font-extrabold text-gray-800">2.4d</span>
                <span class="text-xs font-bold text-[#0f6b52] mb-1">Efficient</span>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-[#f6faf8] p-5 border border-[#dcebe5] rounded-xl mb-6">
        <form method="GET" action="{{ route('admin.returns.jual_beli') }}" class="flex flex-col gap-5">
            <div class="flex flex-wrap gap-8 items-start">
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 mb-2" for="status">Filter Status</label>
                    <div class="relative">
                        <select class="w-48 text-[13px] font-medium text-gray-700 py-2.5 px-3 pr-8 rounded-md bg-white border border-[#dcebe5] appearance-none focus:outline-none focus:border-[#0f6b52]" id="status" name="status" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            @foreach(['pending', 'dispute', 'checking', 'completed', 'rejected'] as $st)
                                <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                        <i data-lucide="chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400" style="width: 14px; height: 14px; pointer-events: none;"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 mb-2" for="from">Rentang Tanggal</label>
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <input class="w-[140px] text-[13px] font-medium text-gray-700 py-2.5 px-3 pr-8 rounded-md bg-white border border-[#dcebe5] focus:outline-none focus:border-[#0f6b52]" type="text" placeholder="dd/mm/yyyy" onfocus="(this.type='date')" id="from" name="from" value="{{ request('from') }}" />
                            <i data-lucide="calendar" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400" style="width: 14px; height: 14px; pointer-events: none;"></i>
                        </div>
                        <span class="text-gray-400 text-sm">-</span>
                        <div class="relative">
                            <input class="w-[140px] text-[13px] font-medium text-gray-700 py-2.5 px-3 pr-8 rounded-md bg-white border border-[#dcebe5] focus:outline-none focus:border-[#0f6b52]" type="text" placeholder="dd/mm/yyyy" onfocus="(this.type='date')" id="to" name="to" value="{{ request('to') }}" />
                            <i data-lucide="calendar" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400" style="width: 14px; height: 14px; pointer-events: none;"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 mb-2">Tipe Marketplace</label>
                    <div class="flex border border-[#dcebe5] rounded-md overflow-hidden bg-white h-[42px]">
                        <button type="button" onclick="document.getElementById('tipe_marketplace').value='produk_fisik'; this.form.submit()" class="px-5 text-[11px] font-bold leading-tight transition-colors {{ request('tipe_marketplace', 'produk_fisik') == 'produk_fisik' ? 'bg-[#0f6b52] text-white' : 'text-gray-500 hover:bg-gray-50 bg-white' }}">Produk<br>Fisik</button>
                        <button type="button" onclick="document.getElementById('tipe_marketplace').value='layanan'; this.form.submit()" class="px-6 text-[12px] font-medium border-l border-[#dcebe5] transition-colors {{ request('tipe_marketplace') == 'layanan' ? 'bg-[#0f6b52] text-white' : 'text-gray-500 hover:bg-gray-50 bg-white' }}">Layanan</button>
                        <input type="hidden" id="tipe_marketplace" name="tipe_marketplace" value="{{ request('tipe_marketplace', 'produk_fisik') }}">
                    </div>
                </div>
            </div>
            <div>
                <a href="{{ route('admin.returns.jual_beli') }}" class="inline-block border border-gray-300 text-gray-600 bg-transparent hover:bg-gray-100/50 px-5 py-2 rounded-md text-[13px] font-semibold transition-colors">Reset Filter</a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="admin-card border border-green-100 rounded-xl overflow-hidden">
        <div class="admin-table-wrap">
            <table class="admin-table w-full text-sm">
                <thead class="bg-[#f8fbf9] border-b border-green-100 text-gray-500 text-xs tracking-wider">
                    <tr>
                        <th class="py-4 px-6 text-left font-bold w-[15%]">ID RETUR</th>
                        <th class="py-4 px-6 text-left font-bold w-[15%]">ID PESANAN</th>
                        <th class="py-4 px-6 text-left font-bold w-[20%]">PENJUAL / PEMBELI</th>
                        <th class="py-4 px-6 text-center font-bold w-[20%]">TOTAL ESCROW</th>
                        <th class="py-4 px-6 text-center font-bold w-[15%]">STATUS</th>
                        <th class="py-4 px-6 text-center font-bold w-[15%]">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($returns as $item)
                        @php
                            $storeName = $item->order->details->first()->product->store->nama_toko ?? 'Toko';
                            $buyerName = $item->order->user->name ?? 'Pembeli';
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-6">
                                <div class="font-bold text-[#0f6b52] text-[13px]">#RET-{{ 88200 + $item->id }}</div>
                                <div class="text-[9px] text-gray-400 mt-0.5">{{ $item->created_at->format('d Okt Y, H:i') }}</div>
                            </td>
                            <td class="py-4 px-6 text-gray-600 text-[13px] font-medium">ORD-{{ 22900000 + $item->order_id }}</td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-gray-800 text-[13px]">{{ $storeName }}</div>
                                <div class="text-[10px] text-gray-500 mt-0.5">{{ $buyerName }}</div>
                            </td>
                            <td class="py-4 px-6 font-semibold text-gray-700 text-center text-[14px]">Rp{{ number_format((int) $item->escrow_total, 0, ',', '.') }}</td>
                            <td class="py-4 px-6 text-center">
                                @if($item->status == 'dispute')
                                    <span class="inline-block px-3 py-1 text-[9px] font-bold uppercase tracking-wider rounded-full bg-red-100 text-red-600 border border-red-200">Mediation</span>
                                @elseif($item->status == 'pending')
                                    <span class="inline-block px-3 py-1 text-[9px] font-bold uppercase tracking-wider rounded-full bg-blue-100 text-blue-600 border border-blue-200">Pending</span>
                                @elseif($item->status == 'checking')
                                    <span class="inline-block px-3 py-1 text-[9px] font-bold uppercase tracking-wider rounded-full bg-green-100 text-green-600 border border-green-200">Approved</span>
                                @elseif($item->status == 'rejected')
                                    <span class="inline-block px-3 py-1 text-[9px] font-bold uppercase tracking-wider rounded-full bg-red-100 text-red-600 border border-red-200">Rejected</span>
                                @elseif($item->status == 'completed')
                                    <span class="inline-block px-3 py-1 text-[9px] font-bold uppercase tracking-wider rounded-full bg-gray-200 text-gray-600 border border-gray-300">Completed</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($item->status == 'dispute')
                                    <a href="{{ route('admin.returns.show', $item->id) }}" class="inline-block bg-red-600 hover:bg-red-700 text-white text-[11px] font-black px-5 py-2 rounded shadow-sm uppercase tracking-wider">Resolusi Sengketa</a>
                                @else
                                    <a href="{{ route('admin.returns.show', $item->id) }}" class="inline-block bg-[#0f6b52] hover:bg-[#0c5843] text-white text-[11px] font-bold px-5 py-2 rounded shadow-sm">Kelola</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">Tidak ada data retur.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 bg-[#f8fbf9] flex items-center justify-between">
            <div class="text-xs text-gray-500 font-medium">
                Menampilkan {{ $returns->firstItem() ?? 0 }}-{{ $returns->lastItem() ?? 0 }} dari {{ number_format($returns->total(), 0, ',', '.') }} permintaan
            </div>
            <div class="flex gap-1">
                {{ $returns->links() }}
            </div>
        </div>
    </div>

    <!-- Bottom Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
        <div class="admin-card bg-[#e6f4ea] border-none p-6 flex gap-4">
            <div class="w-10 h-10 rounded-full bg-[#0f6b52] text-white flex items-center justify-center shrink-0">
                <i data-lucide="scale" style="width:20px; height:20px;"></i>
            </div>
            <div>
                <h3 class="font-bold text-[#0f6b52] mb-2">Panduan Mediasi Hakim</h3>
                <p class="text-xs text-[#0f6b52]/80 leading-relaxed mb-4">Pastikan untuk meninjau bukti foto dan video dari kedua belah pihak sebelum memberikan keputusan akhir pengembalian dana.</p>
                <a href="#" class="text-sm font-bold text-[#0f6b52] hover:underline flex items-center gap-1">
                    Buka Knowledge Base <i data-lucide="external-link" style="width:14px; height:14px;"></i>
                </a>
            </div>
        </div>
        <div class="admin-card bg-gray-900 border-none p-6 text-white relative overflow-hidden flex flex-col justify-end min-h-[140px]">
            <!-- Decorative background or image could go here -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent z-10"></div>
            <div class="absolute inset-0 opacity-20" style="background-image: url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=600&auto=format&fit=crop'); background-size: cover; background-position: center;"></div>
            
            <div class="relative z-20">
                <h3 class="font-bold text-white mb-1">Statistik Logistik</h3>
                <p class="text-xs text-gray-300">94% retur berhasil diselesaikan dalam waktu kurang dari 3 hari bulan ini.</p>
            </div>
        </div>
    </div>
</div>
@endsection
