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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-[24px] border border-slate-100">
                    <div class="space-y-1">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Biaya Sewa</label>
                        <p class="text-sm font-bold text-slate-800">Rp {{ number_format($detail->harga) }}</p>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[10px] font-black text-emerald-500 uppercase tracking-widest">Dana Jaminan (50%)</label>
                        <p class="text-sm font-bold text-emerald-600">Rp {{ number_format($detail->product->buy_price * 0.5) }}</p>
                        <p class="text-[8px] text-slate-400 leading-tight">*Akan dikembalikan utuh jika barang aman.</p>
                    </div>
                    <div class="md:col-span-2 pt-3 border-t border-dashed border-slate-200">
                        <div class="flex justify-between items-center">
                            <label class="block text-xs font-bold text-slate-700">Estimasi Denda Terlambat</label>
                            <p class="text-sm font-black text-red-600">Rp {{ number_format($denda ?? 0) }}</p>
                        </div>
                        <p class="text-[9px] text-slate-400 mt-1 italic">Denda akan dipotong otomatis dari Dana Jaminan Anda.</p>
                    </div>
                </div>

                <div class="p-4 bg-blue-50 border border-blue-100 rounded-2xl flex gap-4 items-start">
                    <div class="shrink-0 text-blue-600">
                        <i data-lucide="shield-check" style="width: 20px; height: 20px;"></i>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-xs font-bold text-blue-900 uppercase tracking-wider">Proteksi Penyewa Campify</h4>
                        <p class="text-[10px] text-blue-700 leading-relaxed">
                            Dana Jaminan Anda tersimpan aman di escrow Campify. Pastikan Anda <strong>{{ auth()->user()->ktp_verified_at ? 'sudah terverifikasi' : 'segera verifikasi KTP' }}</strong> untuk mempercepat proses pencairan refund setelah barang diterima penjual.
                        </p>
                    </div>
                </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-2xl font-bold shadow-lg shadow-emerald-200 transition-all transform hover:-translate-y-1">
                    Ajukan Pengembalian
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
