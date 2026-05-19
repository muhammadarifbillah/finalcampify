@extends('layouts.admin')

@section('title', 'Detail Pengembalian Sewa')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-[10px] font-bold rounded uppercase tracking-wider">RETURN #RT-{{ 99200 + $return->id }}</span>
                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-600 text-[10px] font-bold rounded uppercase tracking-wider">PENGEMBALIAN NORMAL</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900">{{ $return->order->details->first()->product->name ?? 'Produk Sewa' }}</h1>
            <p class="text-sm text-gray-500 font-medium">Order ID: <span class="font-bold text-gray-900">ORD-{{ 22900000 + $return->order_id }}</span></p>
        </div>
        <a href="{{ route('admin.returns.sewa') }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 text-[13px] font-bold rounded-lg hover:bg-gray-50 transition-all active:scale-95">Kembali</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Info Card -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-8">
            <div>
                <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">INFORMASI TRANSAKSI</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                        <span class="text-sm text-gray-500 font-medium">Penyewa</span>
                        <span class="text-sm font-bold text-gray-900">{{ $return->order->user->name }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                        <span class="text-sm text-gray-500 font-medium">Pemilik Toko</span>
                        <span class="text-sm font-bold text-gray-900">{{ $return->order->details->first()->product->store?->nama_toko ?? 'Toko' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                        <span class="text-sm text-gray-500 font-medium">Tanggal Sewa</span>
                        <span class="text-sm font-bold text-gray-900">{{ $return->order->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                        <span class="text-sm text-gray-500 font-medium">SLA Pengembalian</span>
                        <span class="text-sm font-bold text-gray-900">{{ $return->expected_date ? $return->expected_date->format('d M Y, H:i') : '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                        <span class="text-sm text-gray-500 font-medium">Tanggal Kembali Nyata</span>
                        <span class="text-sm font-bold {{ $return->actual_date ? 'text-blue-600' : 'text-gray-400 italic' }}">
                            {{ $return->actual_date ? $return->actual_date->format('d M Y, H:i') : 'Belum Kembali' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-3">
                        <span class="text-sm text-gray-500 font-medium">Status Saat Ini</span>
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
                        <div class="text-[10px] text-gray-500 uppercase font-black tracking-widest">{{ $return->order->details->first()->product->category?->name ?? 'Kategori' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settlement Card -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 space-y-8">
            <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">PENYELESAIAN DANA</h2>

            <div class="space-y-6">
                <div class="bg-[#f4f9f6] p-6 rounded-2xl border border-emerald-100 text-center space-y-1">
                    <div class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Total Escrow</div>
                    <div class="text-3xl font-black text-[#0f6b52]">Rp {{ number_format((int)$return->escrow_total, 0, ',', '.') }}</div>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 font-medium">Denda Keterlambatan</span>
                        <span class="font-bold {{ $return->late_fee > 0 ? 'text-red-600' : 'text-gray-900' }}">Rp {{ number_format((int)$return->late_fee, 0, ',', '.') }}</span>
                    </div>
                    @if($return->damage_fee > 0)
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500 font-medium">Denda Kerusakan</span>
                            <span class="font-bold text-red-600">Rp {{ number_format((int)$return->damage_fee, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 font-medium">Cair ke Pemilik</span>
                        <span class="font-black text-gray-900">Rp {{ number_format((int)$return->to_seller, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm pt-4 border-t border-dashed border-gray-100">
                        <span class="text-gray-500 font-bold italic">Refund ke Penyewa</span>
                        <span class="text-lg font-black text-[#0f6b52]">Rp {{ number_format((int)$return->to_buyer, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="pt-6 space-y-3">
                @if($return->status === 'waiting_refund')
                    <form method="POST" action="{{ route('admin.returns.finalize', $return->id) }}">
                        @csrf
                        <input type="hidden" name="final_status" value="completed">
                        <button type="submit" class="w-full py-4 bg-[#0f6b52] text-white text-sm font-black rounded-xl hover:bg-[#0c5843] transition-all active:scale-95 shadow-lg flex items-center justify-center gap-3 group">
                            <i data-lucide="check-circle" style="width: 20px; height: 20px;"></i>
                            SELESAIKAN & TRANSFER REFUND
                        </button>
                    </form>
                @elseif($return->status === 'completed')
                    <div class="space-y-4">
                        <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-xl flex items-center gap-3">
                            <i data-lucide="check-circle" class="text-emerald-600" style="width: 20px; height: 20px;"></i>
                            <div class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider">Transaksi Telah Selesai (Completed)</div>
                        </div>

                        <!-- Panel Transfer Manual Admin -->
                        @if(!$return->refund_disbursed_at)
                            <div class="p-5 bg-amber-50 border border-amber-100 rounded-2xl space-y-4">
                                <h3 class="text-xs font-black text-amber-800 uppercase tracking-wider"><i class="bi bi-bank me-1"></i>INSTRUKSI PENCAIRAN MANUAL (ADMIN)</h3>
                                
                                <!-- Ke Pembeli -->
                                <div class="p-3 bg-white rounded-xl border border-amber-200 space-y-2">
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-gray-500">Tujuan Refund Penyewa:</span>
                                        <span class="font-bold text-[#0f6b52]">Rp {{ number_format((int)$return->to_buyer, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-1 text-[11px] pt-2 border-t text-gray-700">
                                        <span class="text-gray-400">Bank:</span>
                                        <span class="col-span-2 font-bold">{{ $return->buyer_refund_bank_name ?? '-' }}</span>
                                        <span class="text-gray-400">No. Rek:</span>
                                        <span class="col-span-2 font-bold">{{ $return->buyer_refund_bank_account ?? '-' }}</span>
                                        <span class="text-gray-400 font-bold">Atas Nama:</span>
                                        <span class="col-span-2 font-bold">{{ $return->buyer_refund_bank_name_owner ?? '-' }}</span>
                                    </div>
                                </div>

                                <!-- Ke Penjual -->
                                <div class="p-3 bg-white rounded-xl border border-amber-200 space-y-2">
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-gray-500">Tujuan Transfer Seller:</span>
                                        <span class="font-bold text-gray-900">Rp {{ number_format((int)$return->to_seller, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-1 text-[11px] pt-2 border-t text-gray-700">
                                        <span class="text-gray-400">Bank:</span>
                                        <span class="col-span-2 font-bold">{{ $return->order->details->first()->product->store->bank_name ?? 'BCA' }}</span>
                                        <span class="text-gray-400">No. Rek:</span>
                                        <span class="col-span-2 font-bold">{{ $return->order->details->first()->product->store->bank_account_number ?? '-' }}</span>
                                        <span class="text-gray-400 font-bold">Atas Nama:</span>
                                        <span class="col-span-2 font-bold">{{ $return->order->details->first()->product->store->bank_account_name ?? '-' }}</span>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('admin.returns.disburse', $return->id) }}">
                                    @csrf
                                    <button type="submit" class="w-full py-3 bg-[#0f6b52] hover:bg-[#0c5843] text-white text-xs font-black rounded-xl shadow transition-all active:scale-95 flex items-center justify-center gap-2">
                                        <i data-lucide="check-square" style="width: 16px; height: 16px;"></i>
                                        TANDAI DANA TELAH DITRANSFER
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="p-4 bg-emerald-100 border border-emerald-200 rounded-xl space-y-2 text-emerald-800">
                                <div class="flex items-center gap-2 text-xs font-bold">
                                    <span>✅ DANA TELAH DICAIRKAN</span>
                                </div>
                                <p class="text-[10px] leading-relaxed m-0 text-emerald-700">
                                    Semua pencairan dana jaminan (refund penyewa) & dana sewa (payout seller) telah sukses diselesaikan oleh Admin pada <strong>{{ $return->refund_disbursed_at->format('d M Y H:i') }}</strong>.
                                </p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl text-center space-y-1">
                        <div class="text-xs font-bold text-gray-500 uppercase">Menunggu Proses dari Seller / Penyewa</div>
                        <p class="text-[10px] text-gray-400 leading-normal">
                            Seller belum melakukan konfirmasi penerimaan barang atau pembayaran denda belum diselesaikan.
                        </p>
                    </div>
                @endif
                <p class="text-[9px] text-gray-400 text-center leading-relaxed">Dana akan didistribusikan sesuai kalkulasi di atas. Pastikan kondisi barang sudah diperiksa oleh pemilik.</p>
            </div>
        </div>
    </div>
</div>
@endsection
