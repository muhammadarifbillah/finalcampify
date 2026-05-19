@extends('layouts.admin')

@section('title', 'Detail Retur Jual-Beli')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-[10px] font-bold rounded uppercase tracking-wider">RETURN #RT-{{ 88200 + $return->id }}</span>
                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-600 text-[10px] font-bold rounded uppercase tracking-wider">RETUR NORMAL</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900">{{ $return->order->details->first()->product->name ?? 'Produk' }}</h1>
            <p class="text-sm text-gray-500 font-medium">Order ID: <span class="font-bold text-gray-900">ORD-{{ 22900000 + $return->order_id }}</span></p>
        </div>
        <a href="{{ route('admin.returns.jual_beli') }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 text-[13px] font-bold rounded-lg hover:bg-gray-50 transition-all active:scale-95">Kembali</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Info Card -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-8">
            <div>
                <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">INFORMASI TRANSAKSI</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                        <span class="text-sm text-gray-500 font-medium">Pembeli</span>
                        <span class="text-sm font-bold text-gray-900">{{ $return->order->user->name }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                        <span class="text-sm text-gray-500 font-medium">Toko Penjual</span>
                        <span class="text-sm font-bold text-gray-900">{{ $return->order->details->first()->product->store?->nama_toko ?? 'Toko' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                        <span class="text-sm text-gray-500 font-medium">Tanggal Transaksi</span>
                        <span class="text-sm font-bold text-gray-900">{{ $return->order->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3">
                        <span class="text-sm text-gray-500 font-medium">Status</span>
                        <span class="px-2.5 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black rounded-full uppercase tracking-wider border border-indigo-100">{{ $return->status }}</span>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">PRODUK</h2>
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="w-12 h-12 rounded-lg bg-white border border-gray-200 flex items-center justify-center">
                        <i data-lucide="package" style="width: 24px; height: 24px;" class="text-gray-300"></i>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-gray-900">{{ $return->order->details->first()->product->name ?? '-' }}</div>
                        <div class="text-[10px] text-gray-500 uppercase font-black tracking-widest">{{ $return->order->details->first()->product->store?->nama_toko ?? 'Toko' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Retur Card -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-6">
            <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">DETAIL PENGEMBALIAN</h2>

            <div class="space-y-4 text-sm">
                <div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Alasan Retur</span>
                    <p class="font-bold text-slate-800 mt-1">{{ $return->renter_notes ?? 'Tidak ada alasan' }}</p>
                </div>

                @if($return->proof_returned_image)
                <div class="pt-4 border-t border-gray-50">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Foto / Bukti Kondisi Barang</span>
                    <a href="{{ asset($return->proof_returned_image) }}" target="_blank" class="block rounded-lg overflow-hidden border border-gray-200 hover:border-emerald-500 transition-colors">
                        <img src="{{ asset($return->proof_returned_image) }}" class="w-full h-48 object-cover">
                    </a>
                </div>
                @endif

                @if($return->resi_return)
                <div class="pt-4 border-t border-gray-50">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Nomor Resi / Pengiriman</span>
                    <p class="font-bold text-slate-800 mt-1">{{ $return->resi_return }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
