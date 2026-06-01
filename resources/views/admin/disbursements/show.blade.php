@extends('layouts.admin')

@section('title', 'Detail Pencairan Dana')

@section('content')
@php
    $order = $sellerOrder->order;
    $store = $sellerOrder->store;
    $sellerName = $store?->nama_toko ?? $sellerOrder->seller?->name ?? 'Seller tidak ditemukan';
    $statusKey = $eligibility['status_key'] ?? 'WAITING_DELIVERY';
    $payout = $eligibility['payout'] ?? $sellerOrder->payout;
@endphp

<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
        <div>
            <div class="text-xs text-gray-500 mb-2 flex items-center gap-2">
                <span>Marketplace</span>
                <i data-lucide="chevron-right" style="width: 12px; height: 12px;"></i>
                <a href="{{ route('admin.disbursements.index') }}" class="hover:text-[#0f6b52]">Pencairan Dana</a>
                <i data-lucide="chevron-right" style="width: 12px; height: 12px;"></i>
                <span class="font-semibold text-gray-700">{{ $sellerOrder->seller_order_number ?? 'SO-' . $sellerOrder->id }}</span>
            </div>
            <h1 class="admin-section-title text-2xl font-bold">Detail Pencairan Dana</h1>
            <p class="text-sm text-gray-500 mt-1">
                Seller Order {{ $sellerOrder->seller_order_number ?? 'SO-' . $sellerOrder->id }}
                dari order {{ $order?->order_number ?? '#ORD-' . $sellerOrder->order_id }}
            </p>
        </div>
        <a href="{{ route('admin.disbursements.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-bold rounded-lg hover:bg-gray-50">
            <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="admin-card p-5 rounded-xl border border-gray-100 shadow-sm">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Subtotal Seller Order</div>
            <div class="text-2xl font-extrabold text-gray-900">Rp {{ number_format($sellerOrder->subtotal, 0, ',', '.') }}</div>
        </div>
        <div class="admin-card p-5 rounded-xl border border-gray-100 shadow-sm">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Tanggal Diterima</div>
            <div class="text-lg font-extrabold text-gray-900">{{ $sellerOrder->delivered_at?->format('d M Y H:i') ?? 'Belum delivered' }}</div>
            @if($readyAt)
                <div class="text-[11px] text-gray-500 mt-1">Ready mulai {{ $readyAt->format('d M Y H:i') }}</div>
            @endif
        </div>
        <div class="admin-card p-5 rounded-xl border border-gray-100 shadow-sm">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Status Payout</div>
            @if($statusKey === 'DISBURSED')
                <span class="inline-flex px-2.5 py-1 text-[10px] font-black rounded-full bg-emerald-100 text-emerald-700 uppercase tracking-wider">DISBURSED</span>
                <div class="text-[11px] text-gray-500 mt-2">{{ $payout?->disbursed_at?->format('d M Y H:i') }}</div>
            @elseif($statusKey === 'READY_TO_DISBURSE')
                <span class="inline-flex px-2.5 py-1 text-[10px] font-black rounded-full bg-blue-100 text-blue-700 uppercase tracking-wider">READY_TO_DISBURSE</span>
            @elseif($statusKey === 'WAITING_HOLD')
                <span class="inline-flex px-2.5 py-1 text-[10px] font-black rounded-full bg-amber-100 text-amber-700 uppercase tracking-wider">WAITING_HOLD</span>
                <div class="text-[11px] text-gray-500 mt-2">{{ $daysUntilReady }} hari lagi</div>
            @else
                <span class="inline-flex px-2.5 py-1 text-[10px] font-black rounded-full bg-gray-100 text-gray-600 uppercase tracking-wider">WAITING_DELIVERY</span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="admin-card rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-[#f8fbf9]">
                    <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest">Produk Seller Order</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-[10px] uppercase tracking-widest text-gray-500">
                            <tr>
                                <th class="px-4 py-3 text-left font-black">Produk</th>
                                <th class="px-4 py-3 text-center font-black">Qty</th>
                                <th class="px-4 py-3 text-right font-black">Harga</th>
                                <th class="px-4 py-3 text-right font-black">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($sellerOrder->items as $item)
                                @php
                                    $product = $item->product;
                                    $itemSubtotal = ((int) $item->harga) * ((int) $item->qty);
                                @endphp
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            @if($product?->image_url)
                                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-11 h-11 rounded-lg object-cover border border-gray-100">
                                            @else
                                                <div class="w-11 h-11 rounded-lg bg-gray-50 border border-gray-100 flex items-center justify-center">
                                                    <i data-lucide="package" style="width: 20px; height: 20px;" class="text-gray-300"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-bold text-gray-800">{{ $product?->name ?? 'Produk tidak ditemukan' }}</div>
                                                <div class="text-[10px] text-gray-400 uppercase tracking-wider">{{ $item->type }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold text-gray-700">{{ $item->qty }}</td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-700">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right font-extrabold text-gray-900">Rp {{ number_format($itemSubtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-[#f8fbf9]">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right text-[10px] font-black uppercase tracking-widest text-gray-500">Subtotal Produk</td>
                                <td class="px-4 py-3 text-right font-extrabold text-gray-900">Rp {{ number_format($sellerOrder->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right text-[10px] font-black uppercase tracking-widest text-gray-500">Dana Dicairkan</td>
                                <td class="px-4 py-3 text-right font-extrabold text-emerald-700">Rp {{ number_format($payout?->amount ?? $sellerOrder->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="admin-card p-5 rounded-xl border border-gray-100 shadow-sm space-y-4">
                <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest">Informasi Seller Order</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between gap-4 border-b border-gray-50 pb-3">
                        <span class="text-gray-500">Seller Order</span>
                        <span class="font-bold text-gray-900 text-right">{{ $sellerOrder->seller_order_number ?? 'SO-' . $sellerOrder->id }}</span>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-gray-50 pb-3">
                        <span class="text-gray-500">Order</span>
                        <span class="font-bold text-gray-900 text-right">{{ $order?->order_number ?? '#ORD-' . $sellerOrder->order_id }}</span>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-gray-50 pb-3">
                        <span class="text-gray-500">Seller</span>
                        <span class="font-bold text-gray-900 text-right">{{ $sellerName }}</span>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-gray-50 pb-3">
                        <span class="text-gray-500">Bank</span>
                        <span class="font-bold text-gray-900 text-right">{{ $bankName }}</span>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-gray-50 pb-3">
                        <span class="text-gray-500">Rekening</span>
                        <span class="font-bold text-gray-900 text-right">{{ $bankAccount }}</span>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-gray-50 pb-3">
                        <span class="text-gray-500">Atas Nama</span>
                        <span class="font-bold text-gray-900 text-right">{{ $bankOwner }}</span>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-gray-50 pb-3">
                        <span class="text-gray-500">Pembeli</span>
                        <span class="font-bold text-gray-900 text-right">{{ $order?->buyer?->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-gray-50 pb-3">
                        <span class="text-gray-500">Kurir</span>
                        <span class="font-bold text-gray-900 text-right">{{ strtoupper($sellerOrder->kurir ?? '-') }}</span>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-gray-50 pb-3">
                        <span class="text-gray-500">Resi</span>
                        <span class="font-bold text-gray-900 text-right">{{ $sellerOrder->no_resi ?? '-' }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-gray-500">Alamat Kirim</span>
                        <p class="font-bold text-gray-900 leading-relaxed">{{ $order?->shipping_address ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="admin-card p-5 rounded-xl border border-gray-100 shadow-sm space-y-4">
                <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest">Aksi Pencairan</h2>
                @if($readyToDisburse)
                    <form action="{{ route('admin.disbursements.disburse', $sellerOrder->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin sudah mentransfer dana ke rekening penjual?')">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg bg-[#0f6b52] text-sm font-bold text-white shadow hover:bg-emerald-700 transition">
                            <i data-lucide="send" style="width: 16px; height: 16px;"></i>
                            Tandai Dana Dicairkan
                        </button>
                    </form>
                @elseif($statusKey === 'DISBURSED')
                    <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-100 text-sm font-semibold text-emerald-700">
                        Dana sudah dicairkan pada {{ $payout?->disbursed_at?->format('d M Y H:i') }}.
                    </div>
                @elseif($statusKey === 'WAITING_DELIVERY')
                    <div class="p-4 rounded-lg bg-gray-50 border border-gray-100 text-sm font-semibold text-gray-600">
                        Seller order belum delivered.
                    </div>
                @else
                    <div class="p-4 rounded-lg bg-amber-50 border border-amber-100 text-sm font-semibold text-amber-700">
                        Dana dapat dicairkan setelah periode hold selesai.
                    </div>
                @endif
            </div>
        </aside>
    </div>
</div>
@endsection
