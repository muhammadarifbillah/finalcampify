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
                @if($detail->product->image)
                    <img src="{{ asset($detail->product->image) }}" class="w-16 h-16 object-cover rounded-xl shadow-sm">
                @else
                    <div class="w-16 h-16 bg-slate-200 rounded-xl flex items-center justify-center">🏕️</div>
                @endif
                <div>
                    <h3 class="font-bold text-slate-800">{{ $detail->product->nama_produk }}</h3>
                    <p class="text-xs text-slate-500">Durasi Sewa: {{ $detail->duration }} Hari</p>
                </div>
            </div>

            @if(!$return)
                {{-- TAHAP 1: INPUT RESI --}}
                <form action="{{ route('orders.return.store', $detail->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Toko (Tujuan Pengembalian)</label>
                        <div class="p-4 bg-emerald-50 text-emerald-800 rounded-2xl text-sm border border-emerald-100">
                            <p class="font-bold">{{ $detail->product->store->nama_toko ?? 'Toko Campify' }}</p>
                            <p>{{ $detail->product->store->alamat ?? 'Alamat Toko' }}</p>
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
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Metode Pengembalian</label>
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <label class="flex items-center gap-3 p-4 border border-slate-200 rounded-2xl cursor-pointer hover:border-emerald-500 transition-colors">
                                <input type="radio" name="metode_return" value="antar" checked onclick="toggleResi(false)" class="text-emerald-600 focus:ring-emerald-500">
                                <span class="text-sm font-medium">Antar Langsung</span>
                            </label>
                            <label class="flex items-center gap-3 p-4 border border-slate-200 rounded-2xl cursor-pointer hover:border-emerald-500 transition-colors">
                                <input type="radio" name="metode_return" value="kurir" onclick="toggleResi(true)" class="text-emerald-600 focus:ring-emerald-500">
                                <span class="text-sm font-medium">Kirim via Kurir</span>
                            </label>
                        </div>
                    </div>

                    <div id="resi_container" style="display:none;">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Input Resi Pengembalian</label>
                        <input type="text" name="resi_return" id="resi_input" class="w-full rounded-2xl border-slate-200 p-4 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Masukkan nomor resi pengiriman balik">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Foto/Video Kondisi Barang Saat Ini</label>
                        <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center hover:border-emerald-400 transition">
                            <input type="file" name="foto_kondisi" accept="image/*,video/*" class="w-full text-sm text-slate-600" required />
                            <p class="mt-2 text-[10px] text-slate-500">Wajib diisi sebagai bukti kondisi barang sebelum dikembalikan.</p>
                        </div>
                    </div>

                    <div class="p-4 bg-blue-50 border border-blue-100 rounded-2xl flex gap-4 items-start">
                        <div class="shrink-0 text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.040L3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622l-.618-3.016z"></path></svg>
                        </div>
                        <div class="space-y-1">
                            <h4 class="text-xs font-bold text-blue-900 uppercase tracking-wider">Proteksi Penyewa Campify</h4>
                            <p class="text-[10px] text-blue-700 leading-relaxed">
                                Dana Jaminan Anda tersimpan aman di escrow Campify. Pastikan Anda <strong>{{ auth()->user()->ktp_verified_at ? 'sudah terverifikasi' : 'segera verifikasi KTP' }}</strong> untuk mempercepat proses pencairan refund.
                            </p>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-2xl font-bold shadow-lg shadow-emerald-200 transition-all transform hover:-translate-y-1">
                        Submit Resi Pengembalian
                    </button>
                </form>

                <script>
                    function toggleResi(show) {
                        const container = document.getElementById('resi_container');
                        const input = document.getElementById('resi_input');
                        container.style.display = show ? 'block' : 'none';
                        input.required = show;
                        if(!show) input.value = '';
                    }
                </script>

            @elseif($return && $return->denda > 0 && !$return->bukti_denda)
                {{-- TAHAP 2: UPLOAD BUKTI DENDA --}}
                <div class="space-y-6">
                    <div class="p-6 bg-amber-50 border border-amber-100 rounded-3xl">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-1">Tagihan Denda</p>
                                <p class="text-2xl font-black text-slate-900">Rp {{ number_format($return->denda) }}</p>
                            </div>
                            <span class="px-3 py-1 bg-amber-200 text-amber-800 rounded-full text-[10px] font-bold uppercase">Menunggu Pembayaran</span>
                        </div>
                        
                        <div class="space-y-3 pt-4 border-t border-amber-200/50">
                            <p class="text-xs text-amber-700">Silakan transfer denda ke rekening seller berikut:</p>
                            <div class="bg-white/50 p-4 rounded-2xl border border-amber-200">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-[10px] text-slate-500 uppercase font-bold">Bank</p>
                                        <p class="text-sm font-bold text-slate-800">{{ $detail->product->store->bank_name ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-500 uppercase font-bold">No. Rekening</p>
                                        <p class="text-sm font-bold text-slate-800">{{ $detail->product->store->bank_account_number ?? '-' }}</p>
                                    </div>
                                    <div class="col-span-2">
                                        <p class="text-[10px] text-slate-500 uppercase font-bold">Atas Nama</p>
                                        <p class="text-sm font-bold text-slate-800">{{ $detail->product->store->bank_account_name ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                            <p class="text-[10px] text-amber-600 italic mt-2">*Kondisi Barang: <span class="font-bold uppercase">{{ $return->kondisi_barang }}</span></p>
                        </div>
                    </div>

                    <form action="{{ route('orders.return.upload-bukti', $return->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Upload Bukti Pembayaran Denda</label>
                            <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center hover:border-emerald-400 transition">
                                <input type="file" name="bukti_denda" accept="image/*" class="w-full text-sm text-slate-600" required />
                                <p class="mt-2 text-[10px] text-slate-500">Format: JPG/PNG/WEBP (maks 2MB).</p>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-2xl font-bold shadow-lg shadow-emerald-200 transition-all transform hover:-translate-y-1">
                            Upload Bukti Pembayaran
                        </button>
                    </form>
                </div>

            @elseif($rental->status === 'completed')
                {{-- STATUS: SELESAI --}}
                <div class="text-center py-10">
                    <div class="w-20 h-20 bg-emerald-500 text-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-emerald-200">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-2">Pengembalian Selesai!</h3>
                    <p class="text-slate-500 mb-8">Terima kasih telah mengembalikan alat tepat waktu. Sampai jumpa di petualangan berikutnya!</p>
                    
                    <div class="bg-slate-50 p-6 rounded-3xl text-left border border-slate-100">
                        <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest mb-4">Ringkasan Pengembalian</p>
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Metode</span>
                                <span class="font-bold text-slate-800">{{ $return->resi_return === 'DIANTAR_LANGSUNG' ? 'Antar Langsung' : 'Kurir' }}</span>
                            </div>
                            @if($return->resi_return !== 'DIANTAR_LANGSUNG')
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">No. Resi</span>
                                <span class="font-bold text-slate-800">{{ $return->resi_return }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Kondisi</span>
                                <span class="font-bold text-emerald-600 uppercase">{{ $return->kondisi_barang }}</span>
                            </div>
                            <div class="flex justify-between text-sm pt-3 border-t border-slate-200">
                                <span class="text-slate-500">Total Denda</span>
                                <span class="font-bold text-slate-900">Rp {{ number_format($return->denda) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                {{-- STATUS: MENUNGGU VERIFIKASI TOKO --}}
                <div class="text-center py-10">
                    <div class="w-20 h-20 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Proses Verifikasi</h3>
                    @if($return->denda > 0 && $return->bukti_denda)
                        <p class="text-slate-500">Pembayaran denda Anda sedang dicek oleh seller.</p>
                    @else
                        <p class="text-slate-500">Seller sedang mengecek kondisi barang yang Anda kembalikan.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
