@extends('layouts.admin')

@section('title', 'Pencairan Dana')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <div class="text-xs text-gray-500 mb-1 flex items-center gap-2">
            <span>Marketplace</span> <i data-lucide="chevron-right" style="width: 12px; height: 12px;"></i>
            <span class="font-semibold text-gray-700">Pencairan Dana</span>
        </div>
        <h1 class="admin-section-title text-2xl font-bold">Pencairan Dana ke Penjual</h1>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="admin-card p-5 rounded-xl border border-amber-100 shadow-sm">
            <div class="text-[10px] font-bold text-amber-600 uppercase tracking-widest mb-1">Total Dana Tertahan (Pesanan Selesai)</div>
            <div class="text-2xl font-extrabold text-amber-700">Rp {{ number_format($totalTertahan, 0, ',', '.') }}</div>
        </div>
        <div class="admin-card p-5 rounded-xl border border-emerald-100 shadow-sm">
            <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-1">Total Dana Dicairkan</div>
            <div class="text-2xl font-extrabold text-emerald-700">Rp {{ number_format($totalDicairkan, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="admin-card p-4 rounded-xl border border-gray-100">
        <form method="GET" action="{{ route('admin.disbursements.index') }}" class="flex flex-wrap gap-3 items-center">
            <select name="status" onchange="this.form.submit()"
                class="text-sm py-2 px-4 border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-[#0f6b52]">
                <option value="pending" @selected(request('status') === 'pending' || !request('status'))>Belum Dicairkan (Tertahan)</option>
                <option value="disbursed" @selected(request('status') === 'disbursed')>Sudah Dicairkan</option>
            </select>
        </form>
    </div>

    {{-- Table --}}
    <div class="admin-card rounded-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="admin-table w-full text-sm">
                <thead class="bg-[#f8fbf9] border-b border-gray-100 text-gray-500 text-xs tracking-wider">
                    <tr>
                        <th class="py-4 px-4 text-left font-bold">ID ORDER</th>
                        <th class="py-4 px-4 text-left font-bold">TOKO PENJUAL</th>
                        <th class="py-4 px-4 text-left font-bold">PRODUK</th>
                        <th class="py-4 px-4 text-right font-bold">NOMINAL DANA</th>
                        <th class="py-4 px-4 text-center font-bold">STATUS CAIR</th>
                        <th class="py-4 px-4 text-center font-bold">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        @php
                            $store = $order->details->first()?->product?->store;
                            $storeName = $store->nama_toko ?? '-';
                            $bankName = $store->bank_name ?? $store->user->bank_name ?? '-';
                            $bankAccount = $store->bank_account_number ?? $store->user->bank_account_number ?? '-';
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-4 font-bold text-[#0f6b52]">
                                {{ $order->order_number ?? '#ORD-' . $order->id }}
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-gray-800">{{ $storeName }}</div>
                                <div class="text-[10px] text-gray-500 mt-1">
                                    <i data-lucide="building-2" class="inline w-3 h-3 me-1"></i> {{ $bankName }} - {{ $bankAccount }}
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                @foreach($order->details->take(2) as $detail)
                                    <div class="text-[12px] font-semibold text-gray-700">{{ Str::limit($detail->product->name ?? '-', 25) }}</div>
                                @endforeach
                            </td>
                            <td class="py-4 px-4 text-right">
                                <div class="font-bold text-gray-800">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                            </td>
                            <td class="py-4 px-4 text-center">
                                @if($order->is_disbursed)
                                    <span class="px-2 py-1 text-[9px] font-black rounded-full bg-emerald-100 text-emerald-700 uppercase tracking-wider">Sudah Cair</span>
                                    <div class="text-[10px] text-gray-400 mt-1">{{ $order->disbursed_at?->format('d M Y H:i') }}</div>
                                @else
                                    <span class="px-2 py-1 text-[9px] font-black rounded-full bg-amber-100 text-amber-700 uppercase tracking-wider">Belum Cair</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center">
                                @if(!$order->is_disbursed)
                                    <form action="{{ route('admin.disbursements.disburse', $order->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin sudah mentransfer dana ke rekening penjual?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm bg-[#0f6b52] text-white rounded text-xs px-3 py-1 shadow hover:bg-emerald-700 transition">
                                            Cairkan
                                        </button>
                                    </form>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-gray-400 text-sm">
                                <i data-lucide="inbox" style="width: 32px; height: 32px;" class="mx-auto mb-2 opacity-30"></i>
                                <p>Tidak ada data pencairan dana.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination --}}
        <div class="p-4 border-t border-gray-100 bg-[#f8fbf9] flex items-center justify-between text-xs text-gray-500">
            <div>Menampilkan {{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() }} pesanan</div>
            <div>{{ $orders->links() }}</div>
        </div>
    </div>

</div>
@endsection
