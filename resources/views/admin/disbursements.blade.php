@extends('layouts.admin')

@section('title', 'Pencairan Dana')

@section('content')
<div class="space-y-6">
    <div class="admin-card rounded-[32px] border border-slate-200/70 bg-white/95 shadow-lg overflow-hidden">
        <div class="p-6 lg:p-8">
            <div class="text-xs font-black uppercase tracking-[0.3em] text-emerald-700 mb-4">Pencairan Dana</div>
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl">
                    <h1 class="text-4xl font-black text-slate-900 leading-tight">Kelola Pencairan Seller Order</h1>
                    <p class="mt-4 text-sm text-slate-600 leading-7">Lihat data pencairan, status pengiriman, dan status pencairan semua Seller Order dalam satu dashboard yang lebih rapi.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <form action="{{ route('admin.disbursements.resync') }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 bg-emerald-600 text-white rounded-full text-sm font-semibold hover:bg-emerald-700 transition shadow-sm">Sinkron Ulang Data</button>
                    </form>
                    <a href="{{ route('admin.dashboard.export') }}?type=disbursements&status={{ $status }}" class="inline-flex items-center gap-2 px-5 py-3 bg-slate-900 text-white rounded-full text-sm font-semibold hover:bg-slate-800 transition shadow-sm">Ekspor Pencairan</a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4"> 
        <div class="admin-card rounded-[28px] border border-slate-200/80 bg-white shadow-sm transition hover:shadow-md overflow-hidden">
            <div class="p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-[0.35em] text-slate-400">Total Dana Tertahan</div>
                        <div class="mt-4 text-3xl font-black text-slate-900 leading-tight">Rp {{ number_format($totalTertahan, 0, ',', '.') }}</div>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 shadow-sm">
                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="mt-5 text-sm text-slate-500">Jumlah dana yang masih tertahan dalam proses pencairan.</p>
            </div>
        </div>
        <a href="{{ route('admin.disbursements.index', ['status' => 'DITERIMA']) }}" class="admin-card rounded-[28px] border border-slate-200/80 bg-white shadow-sm transition hover:shadow-md overflow-hidden">
            <div class="p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-[0.35em] text-cyan-500">Seller Order Diterima</div>
                        <div class="mt-4 text-3xl font-black text-slate-900 leading-tight">{{ $deliveredCount }} Seller Order</div>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-50 text-cyan-700 shadow-sm">
                        <i data-lucide="package" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="mt-5 text-sm text-slate-500">Jumlah Seller Order yang sudah diterima.</p>
            </div>
        </a>
        <a href="{{ route('admin.disbursements.index', ['status' => 'READY_TO_DISBURSE']) }}" class="admin-card rounded-[28px] border border-slate-200/80 bg-white shadow-sm transition hover:shadow-md overflow-hidden">
            <div class="p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-[0.35em] text-sky-500">Siap Dicairkan</div>
                        <div class="mt-4 text-3xl font-black text-slate-900 leading-tight">Rp {{ number_format($totalSiapCair, 0, ',', '.') }}</div>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-700 shadow-sm">
                        <i data-lucide="dollar-sign" class="w-5 h-5"></i>
                    </div>
                </div>
                <p class="mt-5 text-sm text-slate-500">{{ $readyCount }} Seller Order siap untuk diproses pencairan.</p>
            </div>
        </a>
    </div>

    <div class="admin-card rounded-[28px] border border-slate-200/80 bg-white/95 overflow-hidden shadow-lg p-5">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-black uppercase tracking-[.22em] text-slate-500">Filter Status</p>
                <p class="mt-2 text-sm text-slate-600">Gunakan menu filter untuk mencari status pencairan dengan cepat.</p>
            </div>
            <form method="GET" action="{{ route('admin.disbursements.index') }}" class="w-full lg:w-auto">
                <label class="sr-only" for="status">Pilih status</label>
                <select id="status" name="status" onchange="this.form.submit()" class="w-full lg:min-w-[320px] text-sm py-3 px-4 border border-slate-200 rounded-2xl bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                    <option value="pending" @selected($status === 'pending')>Semua Belum Dicairkan</option>
                    <option value="WAITING_DELIVERY" @selected($status === 'WAITING_DELIVERY')>Menunggu Pengiriman</option>
                    <option value="WAITING_HOLD" @selected($status === 'WAITING_HOLD')>Menunggu Hold</option>
                    <option value="READY_TO_DISBURSE" @selected($status === 'READY_TO_DISBURSE')>Siap Dicairkan</option>
                    <option value="DITERIMA" @selected($status === 'DITERIMA')>Diterima / Siap Hold</option>
                    <option value="DISBURSED" @selected($status === 'DISBURSED')>Sudah Dicairkan</option>
                </select>
            </form>
        </div>
    </div>

    <div class="admin-card rounded-[28px] border border-slate-200/80 bg-white/95 overflow-hidden shadow-lg">
        <div class="admin-table-wrap">
            <table class="admin-table w-full min-w-full">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs tracking-wider uppercase">
                    <tr>
                        <th class="py-4 px-5 text-left font-semibold">Seller Order</th>
                        <th class="py-4 px-5 text-left font-semibold">Penjual</th>
                        <th class="py-4 px-5 text-left font-semibold">Order Induk</th>
                        <th class="py-4 px-5 text-left font-semibold">Produk</th>
                        <th class="py-4 px-5 text-right font-semibold">Nominal</th>
                        <th class="py-4 px-5 text-center font-semibold">Diterima</th>
                        <th class="py-4 px-5 text-center font-semibold">Status Transaksi</th>
                        <th class="py-4 px-5 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($sellerOrders as $sellerOrder)
                        @php
                            $order = $sellerOrder->order;
                            $store = $sellerOrder->store;
                            $sellerName = $store?->nama_toko ?? $sellerOrder->seller?->name ?? 'Penjual tidak ditemukan';
                            $eligibility = $eligibilityBySellerOrderId[$sellerOrder->id] ?? [];
                            $readyAt = $eligibility['ready_at'] ?? null;
                            $isReady = $eligibility['ready'] ?? false;
                            $statusKey = $eligibility['status_key'] ?? 'WAITING_DELIVERY';
                            $daysUntilReady = $eligibility['days_until_ready'] ?? null;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-4">
                                <div class="font-bold text-[#0f6b52]">{{ $sellerOrder->seller_order_number ?? 'SO-' . $sellerOrder->id }}</div>
                                <div class="text-[10px] text-gray-400">ID {{ $sellerOrder->id }}</div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-gray-800">{{ $sellerName }}</div>
                                <div class="text-[10px] text-gray-500 mt-1">{{ $sellerOrder->seller?->email ?? $store?->user?->email ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-gray-800">{{ $order?->order_number ?? '#ORD-' . $sellerOrder->order_id }}</div>
                                <div class="text-[10px] text-gray-500 mt-1">Pembeli: {{ $order?->buyer?->name ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-4">
                                @foreach($sellerOrder->items->take(2) as $item)
                                    <div class="text-[12px] font-semibold text-gray-700">{{ \Illuminate\Support\Str::limit($item->product?->name ?? '-', 25) }}</div>
                                @endforeach
                                @if($sellerOrder->items->count() > 2)
                                    <div class="text-[10px] text-gray-400">+{{ $sellerOrder->items->count() - 2 }} produk lainnya</div>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-right">
                                <div class="font-bold text-gray-800">Rp {{ number_format($sellerOrder->subtotal, 0, ',', '.') }}</div>
                            </td>
                            <td class="py-4 px-4 text-center">
                                @php
                                    $receivedAt = $order?->received_at;
                                    $deliveredAt = $sellerOrder->delivered_at;
                                    $shippedAt = $sellerOrder->shipped_at;
                                    $statusLabel = $sellerOrder->status_label ?? 'Menunggu';
                                @endphp
                                @if($receivedAt)
                                    <div class="text-xs font-bold text-gray-800">Diterima: {{ $receivedAt->format('d M Y') }}</div>
                                    @if($readyAt)
                                        <div class="text-[10px] text-gray-400">Siap: {{ $readyAt->format('d M Y') }}</div>
                                    @endif
                                @elseif($deliveredAt)
                                    <div class="text-xs font-bold text-gray-800">Dikirim: {{ $deliveredAt->format('d M Y') }}</div>
                                    <div class="text-[10px] text-gray-400">Siap: {{ $readyAt?->format('d M Y') ?? '-' }}</div>
                                @elseif($shippedAt)
                                    <div class="text-xs font-bold text-gray-800">Dikirim: {{ $shippedAt->format('d M Y') }}</div>
                                    <div class="text-[10px] text-gray-400">Status SO: {{ $statusLabel }}</div>
                                @elseif($sellerOrder->status === \App\Models\SellerOrder::STATUS_CANCELLED)
                                    <span class="admin-badge admin-badge-danger">Dibatalkan</span>
                                @elseif($sellerOrder->status === \App\Models\SellerOrder::STATUS_PROCESSING)
                                    <span class="admin-badge bg-slate-100 text-slate-700">Sedang diproses</span>
                                @else
                                    <span class="admin-badge bg-slate-100 text-slate-500">Menunggu Diterima</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center">
                                @php
                                    $transactionStatusLabel = match ($statusKey) {
                                        'DISBURSED' => 'Sudah Dicairkan',
                                        'READY_TO_DISBURSE' => 'Siap Dicairkan',
                                        'WAITING_HOLD' => 'Menunggu Hold',
                                        default => 'Menunggu Diterima',
                                    };
                                    $transactionStatusClasses = match ($statusKey) {
                                        'DISBURSED' => 'admin-badge admin-badge-success',
                                        'READY_TO_DISBURSE' => 'admin-badge bg-blue-100 text-blue-700',
                                        'WAITING_HOLD' => 'admin-badge admin-badge-warning',
                                        default => 'admin-badge bg-slate-100 text-slate-600',
                                    };
                                    $statusCaption = match ($sellerOrder->status) {
                                        \App\Models\SellerOrder::STATUS_PENDING => 'Status SO: Menunggu',
                                        \App\Models\SellerOrder::STATUS_PROCESSING => 'Status SO: Diproses',
                                        \App\Models\SellerOrder::STATUS_SHIPPED => 'Status SO: Dikirim',
                                        \App\Models\SellerOrder::STATUS_DELIVERED => 'Status SO: Selesai',
                                        \App\Models\SellerOrder::STATUS_CANCELLED => 'Status SO: Dibatalkan',
                                        default => null,
                                    };
                                @endphp

                                <span class="{{ $transactionStatusClasses }}">{{ $transactionStatusLabel }}</span>
                                @if($statusKey === 'WAITING_HOLD')
                                    <div class="text-[11px] text-slate-500 mt-1">{{ $daysUntilReady }} hari lagi</div>
                                @endif
                                @if($statusCaption)
                                    <div class="text-[11px] text-slate-500 mt-1">{{ $statusCaption }}</div>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center">
                                <div class="grid gap-2 sm:grid-flow-col sm:auto-cols-max sm:justify-center">
                                    <a href="{{ route('admin.disbursements.show', $sellerOrder->id) }}" class="inline-flex items-center justify-center px-4 py-2 rounded-full border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-sm">Detail</a>
                                    @if($isReady)
                                        <form action="{{ route('admin.disbursements.disburse', ['sellerOrder' => $sellerOrder->id]) }}" method="POST" class="m-0" onsubmit="return confirm('Apakah Anda yakin sudah mentransfer dana ke rekening penjual?')">
                                            @csrf
                                            @method('POST')
                                            <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 rounded-full bg-gradient-to-r from-emerald-600 to-emerald-700 text-xs font-bold text-white shadow-lg transition duration-200 transform hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-emerald-400">Cairkan</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-gray-400 text-sm">
                                <i data-lucide="inbox" style="width: 32px; height: 32px;" class="mx-auto mb-2 opacity-30"></i>
                                <p>Tidak ada data pencairan dana.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 bg-[#f8fbf9] flex items-center justify-between text-xs text-gray-500">
            <div>Menampilkan {{ $sellerOrders->firstItem() ?? 0 }}-{{ $sellerOrders->lastItem() ?? 0 }} dari {{ $sellerOrders->total() }} Seller Order</div>
            <div>{{ $sellerOrders->links() }}</div>
        </div>
    </div>
</div>
@endsection
