@extends('layouts.app_pembeli')

@section('content')
<div class="pt-28 pb-20 bg-slate-50 min-h-screen">
    <div class="max-w-2xl mx-auto px-4">
        
        <div class="mb-8">
            <a href="{{ route('orders.detail', $pesanan->id) }}" class="text-sm text-slate-500 hover:text-slate-800 flex items-center gap-2">
                ← Kembali ke Detail Pesanan
            </a>
        </div>

        <div class="bg-white rounded-[32px] shadow-xl p-8 border border-slate-100">
            <h1 class="text-2xl font-bold text-slate-900 mb-6">Pengembalian Produk</h1>

            <div class="flex items-center gap-4 mb-8 p-4 bg-slate-50 rounded-2xl">
                <img src="{{ asset($detail->product->image) }}" class="w-16 h-16 object-cover rounded-xl shadow-sm">
                <div>
                    <h3 class="font-bold text-slate-800">{{ $detail->product->name }}</h3>
                    <p class="text-xs text-slate-500">Durasi Sewa: {{ $detail->duration }} Hari</p>
                </div>
            </div>

            <form action="{{ route('orders.return.store', $detail->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Toko (Tujuan Pengembalian)</label>
                    <div class="p-4 bg-emerald-50 text-emerald-800 rounded-2xl text-sm border border-emerald-100">
                        <p class="font-bold">Campify Outdoor Store</p>
                        <p>Jl. Petualangan No. 123, Kota Bandung, Jawa Barat</p>
                        <p class="mt-1 text-xs opacity-75">Silakan kirimkan barang ke alamat di atas.</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Total Denda</label>
                        <p class="text-lg font-bold text-slate-900">Rp {{ number_format($denda ?? 0) }}</p>
                        <p class="text-[10px] text-slate-500">Dihitung otomatis jika terlambat mengembalikan.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Status Barang</label>
                        @if(!empty($return))
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-bold uppercase">Return Terkirim</span>
                        @elseif(($daysLate ?? 0) > 0)
                            <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-[10px] font-bold uppercase">Terlambat</span>
                        @else
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-[10px] font-bold uppercase">Dalam Masa Sewa</span>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Input Resi Pengembalian</label>
                    <input type="text" name="resi_return" class="w-full rounded-2xl border-slate-200 p-4 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Masukkan nomor resi pengiriman balik" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Kondisi Barang</label>
                    <select name="kondisi_barang" class="w-full rounded-2xl border-slate-200 p-4 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="baik">Baik</option>
                        <option value="rusak">Rusak</option>
                        <option value="hilang">Hilang</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Bukti Pembayaran Denda (Jika Ada)</label>
                    <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center hover:border-emerald-400 transition">
                        <input type="file" name="bukti_denda" accept="image/*" class="w-full text-sm text-slate-600" />
                        <p class="mt-2 text-[10px] text-slate-500">Format: JPG/PNG/WEBP (maks 2MB). Wajib jika ada denda.</p>
                    </div>
                </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-2xl font-bold shadow-lg shadow-emerald-200 transition-all transform hover:-translate-y-1">
                    Submit Pengembalian
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
