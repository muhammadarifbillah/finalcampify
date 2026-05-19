@extends('layouts.admin')

@section('title', 'Kelola Transaksi')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <div class="text-xs text-gray-500 mb-1 flex items-center gap-2">
            <span>Marketplace</span> <i data-lucide="chevron-right" style="width: 12px; height: 12px;"></i>
            <span class="font-semibold text-gray-700">Riwayat Transaksi</span>
        </div>
        <h1 class="admin-section-title text-2xl font-bold">Riwayat Transaksi Pembeli</h1>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Card 1: Total Transaksi --}}
        <div class="bg-white border border-slate-200 hover:border-[#059669] hover:shadow-[0_12px_30px_-10px_rgba(5,150,105,0.2)] rounded-2xl p-6 shadow-sm transition-all duration-300 relative overflow-hidden flex flex-col justify-between group">
            <div class="absolute top-0 left-0 w-full h-[4px] bg-[#059669]"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Transaksi</span>
                <div class="w-8 h-8 rounded-lg bg-[#ecfdf5] text-[#059669] flex items-center justify-center transition-colors group-hover:bg-[#059669] group-hover:text-white">
                    <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ number_format($totalOrders) }}</div>
        </div>

        {{-- Card 2: Total Pendapatan --}}
        <div class="bg-white border border-slate-200 hover:border-[#064e3b] hover:shadow-[0_12px_30px_-10px_rgba(6,78,59,0.2)] rounded-2xl p-6 shadow-sm transition-all duration-300 relative overflow-hidden flex flex-col justify-between group">
            <div class="absolute top-0 left-0 w-full h-[4px] bg-[#064e3b]"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Pendapatan</span>
                <div class="w-8 h-8 rounded-lg bg-[#e6f4ea] text-[#047857] flex items-center justify-center transition-colors group-hover:bg-[#064e3b] group-hover:text-white">
                    <i data-lucide="banknote" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-xl font-extrabold text-[#064e3b] tracking-tight">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        </div>

        {{-- Card 3: Menunggu --}}
        <div class="bg-white border border-slate-200 hover:border-[#b45309] hover:shadow-[0_12px_30px_-10px_rgba(180,83,9,0.2)] rounded-2xl p-6 shadow-sm transition-all duration-300 relative overflow-hidden flex flex-col justify-between group">
            <div class="absolute top-0 left-0 w-full h-[4px] bg-[#b45309]"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Menunggu</span>
                <div class="w-8 h-8 rounded-lg bg-[#fffbeb] text-[#b45309] flex items-center justify-center transition-colors group-hover:bg-[#b45309] group-hover:text-white">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-[#b45309] tracking-tight">{{ number_format($pendingOrders) }}</div>
        </div>

        {{-- Card 4: Selesai --}}
        <div class="bg-white border border-slate-200 hover:border-[#10b981] hover:shadow-[0_12px_30px_-10px_rgba(16,185,129,0.2)] rounded-2xl p-6 shadow-sm transition-all duration-300 relative overflow-hidden flex flex-col justify-between group">
            <div class="absolute top-0 left-0 w-full h-[4px] bg-[#10b981]"></div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Selesai</span>
                <div class="w-8 h-8 rounded-lg bg-[#f0fdf4] text-[#10b981] flex items-center justify-center transition-colors group-hover:bg-[#10b981] group-hover:text-white">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-[#059669] tracking-tight">{{ number_format($selesaiOrders) }}</div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="admin-card p-4 rounded-xl border border-gray-100">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap gap-3 items-center">
            <div class="relative flex-1 min-w-[200px]">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" style="width: 14px; height: 14px;"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama pembeli atau ID order..."
                    class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-[#0f6b52]">
            </div>
            <select name="status" onchange="this.form.submit()"
                class="text-sm py-2 px-4 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-[#0f6b52]">
                <option value="">Semua Status</option>
                @foreach(['menunggu', 'diproses', 'dikirim', 'selesai', 'dibatalkan'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
            <select name="type" onchange="this.form.submit()"
                class="text-sm py-2 px-4 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-[#0f6b52]">
                <option value="">Semua Jenis</option>
                <option value="buy" @selected(request('type') === 'buy')>Pembelian</option>
                <option value="rent" @selected(request('type') === 'rent')>Penyewaan</option>
            </select>
            @if(request('status') || request('search') || request('type'))
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-gray-400 hover:text-gray-700 flex items-center gap-1">
                    <i data-lucide="x" style="width: 12px; height: 12px;"></i> Reset
                </a>
            @endif
            <button type="submit" class="px-4 py-2 bg-[#0f6b52] text-white text-sm font-bold rounded-lg hover:bg-[#0c5843] transition">
                Cari
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="admin-card rounded-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="admin-table w-full text-sm">
                <thead class="bg-[#f8fbf9] border-b border-gray-100 text-gray-500 text-xs tracking-wider">
                    <tr>
                        <th class="py-4 px-4 text-left font-bold">ID ORDER</th>
                        <th class="py-4 px-4 text-left font-bold">PEMBELI</th>
                        <th class="py-4 px-4 text-left font-bold">PRODUK</th>
                        <th class="py-4 px-4 text-left font-bold">TOKO PENJUAL</th>
                        <th class="py-4 px-4 text-center font-bold">JENIS</th>
                        <th class="py-4 px-4 text-right font-bold">TOTAL</th>
                        <th class="py-4 px-4 text-center font-bold">STATUS</th>
                        <th class="py-4 px-4 text-center font-bold">KURIR</th>
                        <th class="py-4 px-4 text-left font-bold">TANGGAL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        @php
                            $isRent = $order->details->where('type', 'rent')->isNotEmpty();
                            $isBuy = $order->details->where('type', 'buy')->isNotEmpty();
                            $storeName = $order->details->first()?->product?->store?->nama_toko ?? '-';

                            $statusMap = [
                                'menunggu'   => 'bg-amber-100 text-amber-700',
                                'diproses'   => 'bg-blue-100 text-blue-700',
                                'dikirim'    => 'bg-indigo-100 text-indigo-700',
                                'selesai'    => 'bg-emerald-100 text-emerald-700',
                                'dibatalkan' => 'bg-gray-100 text-gray-600',
                            ];
                            $badgeClass = $statusMap[$order->status] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-4 font-bold text-[#0f6b52]">
                                #ORD-{{ 22900000 + $order->id }}
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-gray-800">{{ $order->buyer->name ?? '-' }}</div>
                                <div class="text-[10px] text-gray-400">{{ $order->buyer->email ?? '' }}</div>
                            </td>
                            <td class="py-4 px-4">
                                @foreach($order->details->take(2) as $detail)
                                    <div class="text-[12px] font-semibold text-gray-700">{{ Str::limit($detail->product->name ?? '-', 25) }}</div>
                                @endforeach
                                @if($order->details->count() > 2)
                                    <div class="text-[10px] text-gray-400">+{{ $order->details->count() - 2 }} produk lainnya</div>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <div class="text-[12px] font-semibold text-gray-700">{{ $storeName }}</div>
                            </td>
                            <td class="py-4 px-4 text-center">
                                @if($isRent)
                                    <span class="px-2 py-1 text-[9px] font-black rounded-full bg-blue-100 text-blue-700 uppercase tracking-wider">Sewa</span>
                                @endif
                                @if($isBuy)
                                    <span class="px-2 py-1 text-[9px] font-black rounded-full bg-violet-100 text-violet-700 uppercase tracking-wider">Beli</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-right">
                                <div class="font-bold text-gray-800">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                                @if($isRent)
                                    <div class="text-[9px] text-blue-500 font-bold">Incl. Jaminan</div>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="inline-block px-2.5 py-1 text-[9px] font-bold tracking-wider rounded-full {{ $badgeClass }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <div class="text-xs text-gray-600 font-medium">{{ strtoupper($order->kurir ?? '-') }}</div>
                                @if($order->no_resi)
                                    <div class="text-[10px] text-gray-400">{{ $order->no_resi }}</div>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <div class="text-xs text-gray-700">{{ $order->created_at?->format('d M Y') }}</div>
                                <div class="text-[10px] text-gray-400">{{ $order->created_at?->format('H:i') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-10 text-center text-gray-400 text-sm">
                                <i data-lucide="inbox" style="width: 32px; height: 32px;" class="mx-auto mb-2 opacity-30"></i>
                                <p>Tidak ada transaksi ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination --}}
        <div class="p-4 border-t border-gray-100 bg-[#f8fbf9] flex items-center justify-between text-xs text-gray-500">
            <div>Menampilkan {{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() }} transaksi</div>
            <div>{{ $orders->links() }}</div>
        </div>
    </div>

</div>
@endsection
