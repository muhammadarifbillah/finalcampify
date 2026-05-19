@extends('layouts.app_pembeli')

@section('content')
<div class="pt-28 pb-20 bg-slate-50 min-h-screen">
    <div class="max-w-2xl mx-auto px-4">
        
        <div class="mb-6">
            <a href="{{ route('orders.detail', $pesanan->id) }}" class="text-sm text-slate-500 hover:text-slate-800 flex items-center gap-2">
                ← Kembali ke Detail Pesanan
            </a>
        </div>

        <!-- Progress Stepper (Khusus Sewa) -->
        @if($detail->type === 'rent')
        <div class="bg-white rounded-[24px] shadow-sm border border-slate-100 p-6 mb-6">
            <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-slate-400">
                <span>Status Pengembalian</span>
                <span class="text-emerald-600">Sewa</span>
            </div>
            
            <div class="mt-4 flex items-center justify-between relative">
                <!-- Line Behind Stepper -->
                <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-[2px] bg-slate-100 z-0"></div>
                
                @php
                    $status = $return ? $return->status : 'start';
                    $step = 1;
                    if ($status === 'pending') $step = 2;
                    if ($status === 'approved') $step = 3;
                    if ($status === 'shipping') $step = 4;
                    if ($status === 'checking') $step = 4;
                    if (in_array($status, ['denda_pending', 'denda_submitted'])) $step = 5;
                    if ($status === 'waiting_refund') $step = 6;
                    if ($status === 'completed') $step = 7;
                @endphp

                <!-- Step 1: Ajukan -->
                <div class="z-10 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 1 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200' : 'bg-slate-100 text-slate-400' }}">1</div>
                    <span class="text-[9px] font-bold text-slate-500 mt-2">Pengajuan</span>
                </div>
                
                <!-- Step 2: Persetujuan -->
                <div class="z-10 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 2 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200' : 'bg-slate-100 text-slate-400' }}">2</div>
                    <span class="text-[9px] font-bold text-slate-500 mt-2">Persetujuan</span>
                </div>

                <!-- Step 3: Kirim -->
                <div class="z-10 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 3 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200' : 'bg-slate-100 text-slate-400' }}">3</div>
                    <span class="text-[9px] font-bold text-slate-500 mt-2">Kirim</span>
                </div>

                <!-- Step 4: Pengecekan -->
                <div class="z-10 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 4 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200' : 'bg-slate-100 text-slate-400' }}">4</div>
                    <span class="text-[9px] font-bold text-slate-500 mt-2">Pengecekan</span>
                </div>

                <!-- Step 5: Denda (Jika ada) -->
                <div class="z-10 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 5 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200' : 'bg-slate-100 text-slate-400' }}">5</div>
                    <span class="text-[9px] font-bold text-slate-500 mt-2">Denda</span>
                </div>

                <!-- Step 6: Refund -->
                <div class="z-10 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 6 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200' : 'bg-slate-100 text-slate-400' }}">6</div>
                    <span class="text-[9px] font-bold text-slate-500 mt-2">Refund</span>
                </div>

                <!-- Step 7: Selesai -->
                <div class="z-10 flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $step >= 7 ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200' : 'bg-slate-100 text-slate-400' }}">7</div>
                    <span class="text-[9px] font-bold text-slate-500 mt-2">Selesai</span>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-[32px] shadow-xl p-8 border border-slate-100">
            <h1 class="text-2xl font-bold text-slate-900 mb-6">Pengembalian Produk</h1>

            <!-- Info Produk -->
            <div class="flex items-center gap-4 mb-8 p-4 bg-slate-50 rounded-2xl">
                @if($detail->product->image)
                    <img src="{{ asset($detail->product->image) }}" class="w-16 h-16 object-cover rounded-xl shadow-sm">
                @else
                    <div class="w-16 h-16 bg-slate-200 rounded-xl flex items-center justify-center">🏕️</div>
                @endif
                <div>
                    <h3 class="font-bold text-slate-800">{{ $detail->product->name ?? $detail->product->nama_produk }}</h3>
                    <p class="text-xs text-slate-500">
                        {{ $detail->type === 'rent' ? 'Durasi Sewa: ' . ($detail->duration ?? 3) . ' Hari' : 'Jumlah Beli: ' . ($detail->qty ?? 1) . ' Item' }}
                    </p>
                </div>
            </div>

            <!-- Pesan Sukses / Error Flash -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 text-emerald-800 rounded-2xl text-sm border border-emerald-100">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 text-rose-800 rounded-2xl text-sm border border-rose-100">
                    {{ session('error') }}
                </div>
            @endif

            <!-- ------------------ STAGE 1: BELUM DIAJUKAN (PILIH METODE & REKENING) ------------------ -->
            @if(!$return)
                <form action="{{ route('orders.return.store', $detail->id) }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Toko (Tujuan Pengembalian)</label>
                        <div class="p-4 bg-emerald-50 text-emerald-800 rounded-2xl text-sm border border-emerald-100">
                            <p class="font-bold text-base">{{ $detail->product->store->nama_toko ?? 'Toko Campify' }}</p>
                            <p class="mt-1 text-slate-600">{{ $detail->product->store->alamat ?? 'Alamat Toko tidak terintegrasi' }}</p>
                            <p class="mt-2 text-xs opacity-75 font-semibold">Toko ini yang bertanggung jawab untuk menerima barang pengembalian.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-[24px] border border-slate-100">
                        @if($detail->type === 'rent')
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Biaya Sewa</label>
                                <p class="text-sm font-bold text-slate-800">Rp {{ number_format($detail->harga) }}</p>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black text-emerald-500 uppercase tracking-widest">Dana Jaminan</label>
                                <p class="text-sm font-bold text-emerald-600">Rp {{ number_format($detail->product->escrow_amount > 0 ? $detail->product->escrow_amount : ($detail->product->buy_price * 0.25)) }}</p>
                            </div>
                        @else
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Harga Barang</label>
                                <p class="text-sm font-bold text-slate-800">Rp {{ number_format($detail->harga * $detail->qty) }}</p>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] font-black text-emerald-500 uppercase tracking-widest">Estimasi Refund</label>
                                <p class="text-sm font-bold text-emerald-600">100% (Rp {{ number_format($detail->harga * $detail->qty) }})</p>
                            </div>
                        @endif
                    </div>

                    @if($detail->type === 'rent')
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Metode Pengembalian</label>
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <label class="flex items-center gap-3 p-4 border border-slate-200 rounded-2xl cursor-pointer hover:border-emerald-500 transition-colors">
                                <input type="radio" name="metode_return" value="antar" checked class="text-emerald-600 focus:ring-emerald-500">
                                <div>
                                    <span class="text-sm font-bold block text-slate-800">Antar Langsung</span>
                                    <span class="text-[10px] text-slate-400 block">Selesaikan di tempat seller</span>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-4 border border-slate-200 rounded-2xl cursor-pointer hover:border-emerald-500 transition-colors">
                                <input type="radio" name="metode_return" value="kurir" class="text-emerald-600 focus:ring-emerald-500">
                                <div>
                                    <span class="text-sm font-bold block text-slate-800">Kirim via Kurir</span>
                                    <span class="text-[10px] text-slate-400 block">Kirim paket ke alamat toko</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Input Informasi Rekening Bank Pembeli (Untuk Refund Jaminan) -->
                    <div class="bg-slate-50 p-6 rounded-[24px] border border-slate-100 space-y-4">
                        <h4 class="text-sm font-bold text-slate-800">Rekening Pengembalian Dana Jaminan</h4>
                        <p class="text-xs text-slate-500">Uang jaminan sewa Anda akan ditransfer balik oleh Admin ke rekening ini setelah pengembalian barang selesai.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1">Nama Bank</label>
                                <input type="text" name="buyer_refund_bank_name" class="w-full text-xs rounded-xl border-slate-200 p-3 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Contoh: BCA, Mandiri, BRI" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 mb-1">Nomor Rekening</label>
                                <input type="text" name="buyer_refund_bank_account" class="w-full text-xs rounded-xl border-slate-200 p-3 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Nomor rekening tanpa spasi" required>
                            </div>
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-xs font-bold text-slate-600 mb-1">Nama Pemilik Rekening</label>
                                <input type="text" name="buyer_refund_bank_name_owner" class="w-full text-xs rounded-xl border-slate-200 p-3 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Nama lengkap sesuai buku tabungan" required>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($detail->type === 'buy')
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Alasan Retur / Masalah Barang</label>
                        <textarea name="alasan_return" rows="3" class="w-full rounded-2xl border-slate-200 p-4 focus:ring-red-500 focus:border-red-500 text-sm" placeholder="Jelaskan detail masalah barang (misal: rusak, tidak sesuai deskripsi, dll)" required></textarea>
                    </div>
                    @endif

                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-2xl font-bold shadow-lg shadow-emerald-200 transition-all transform hover:-translate-y-1">
                        @if($detail->type === 'rent')
                            Ajukan Permintaan Pengembalian
                        @else
                            Submit Retur Pembelian
                        @endif
                    </button>
                </form>

            <!-- ------------------ STAGE 2: PENDING (MENUNGGU PERSETUJUAN SELLER) ------------------ -->
            @elseif($return && $return->status === 'pending')
                <div class="text-center py-10">
                    <div class="w-20 h-20 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Menunggu Persetujuan Penjual</h3>
                    <p class="text-slate-500 max-w-md mx-auto text-sm">
                        Permintaan pengembalian sewa Anda telah diajukan. Harap tunggu Penjual menyetujui permintaan pengiriman Anda sebelum mengirim barang.
                    </p>
                    
                    <div class="mt-8 bg-slate-50 p-6 rounded-2xl text-left border border-slate-100 space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Detail Rekening Refund Terinput</h4>
                        <div class="grid grid-cols-3 gap-2 text-xs">
                            <span class="text-slate-400">Bank:</span>
                            <span class="col-span-2 font-bold text-slate-800">{{ $return->buyer_refund_bank_name }}</span>
                            <span class="text-slate-400">No. Rekening:</span>
                            <span class="col-span-2 font-bold text-slate-800">{{ $return->buyer_refund_bank_account }}</span>
                            <span class="text-slate-400">Atas Nama:</span>
                            <span class="col-span-2 font-bold text-slate-800">{{ $return->buyer_refund_bank_name_owner }}</span>
                        </div>
                    </div>
                </div>

            <!-- ------------------ STAGE 3: APPROVED (BISA INPUT RESI & FOTO KONDISI) ------------------ -->
            @elseif($return && $return->status === 'approved')
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl">
                    <p class="text-sm font-bold text-emerald-800">✅ Permintaan Pengembalian Disetujui!</p>
                    <p class="text-xs text-emerald-600 mt-1">Penjual menyetujui pengembalian Anda. Silakan kirim barang ke alamat toko dan isi resi di bawah ini.</p>
                </div>

                <form action="{{ route('orders.return.submit-shipping', $return->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Toko (Kirim Paket Ke Sini)</label>
                        <div class="p-4 bg-slate-50 text-slate-700 rounded-2xl text-sm border border-slate-200">
                            <p class="font-bold text-base">{{ $detail->product->store->nama_toko ?? 'Toko Campify' }}</p>
                            <p class="mt-1 text-slate-600">{{ $detail->product->store->alamat ?? 'Alamat Toko' }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Input Resi Pengiriman Balik</label>
                        <input type="text" name="resi_return" class="w-full rounded-2xl border-slate-200 p-4 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Masukkan nomor resi ekspedisi (misal: JNE, J&T, SiCepat)" required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Foto / Bukti Kondisi Barang Saat Ini</label>
                        <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center hover:border-emerald-400 transition cursor-pointer relative bg-slate-50">
                            <input type="file" name="foto_kondisi" accept="image/*" class="w-full text-sm text-slate-600" required />
                            <p class="mt-2 text-[10px] text-slate-500">Wajib diisi sebagai bukti dokumentasi barang dikirim balik dalam kondisi aman.</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Catatan Tambahan (Opsional)</label>
                        <textarea name="renter_notes" rows="2" class="w-full rounded-2xl border-slate-200 p-4 focus:ring-emerald-500 focus:border-emerald-500 text-sm" placeholder="Contoh: Barang sudah dibersihkan dan dipacking rapi."></textarea>
                    </div>

                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-2xl font-bold shadow-lg shadow-emerald-200 transition-all transform hover:-translate-y-1">
                        Kirim Informasi Pengiriman
                    </button>
                </form>

            <!-- ------------------ STAGE 4: SHIPPING (MENUNGGU BARANG DITERIMA SELLER) ------------------ -->
            @elseif($return && $return->status === 'shipping')
                <div class="text-center py-10">
                    <div class="w-20 h-20 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Paket Sedang Dikirim</h3>
                    <p class="text-slate-500 max-w-md mx-auto text-sm">
                        Nomor resi <strong>{{ $return->resi_return }}</strong> telah diunggah. Menunggu Penjual menerima paket dan memeriksa kondisi fisik barang secara langsung.
                    </p>

                    @if($return->proof_returned_image)
                    <div class="mt-6">
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2 text-left">Foto Kondisi Barang yang Diunggah</p>
                        <img src="{{ asset($return->proof_returned_image) }}" class="w-full max-h-60 object-cover rounded-2xl shadow-sm border border-slate-200">
                    </div>
                    @endif
                </div>

            <!-- ------------------ STAGE 5: DENDA PENDING (PEMBELI HARUS BAYAR SISA KEKURANGAN) ------------------ -->
            @elseif($return && $return->status === 'denda_pending')
                <div class="space-y-6">
                    <div class="p-6 bg-amber-50 border border-amber-100 rounded-3xl">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-1">Kekurangan Pembayaran Denda (Defisit)</p>
                                <p class="text-3xl font-black text-rose-600">Rp {{ number_format($return->deficit) }}</p>
                            </div>
                            <span class="px-3 py-1 bg-amber-200 text-amber-800 rounded-full text-[10px] font-bold uppercase">Harus Ditransfer</span>
                        </div>

                        <div class="space-y-4 pt-4 border-t border-amber-200/50 text-slate-700 text-sm">
                            <p class="font-bold text-slate-800">Mengapa saya harus membayar kekurangan ini?</p>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Denda sewa Anda melebihi jumlah deposit jaminan. Deposit jaminan sebesar <strong>Rp {{ number_format($return->deposit_amount) }}</strong> telah terpotong habis, dan Anda wajib melunasi sisa kekurangannya.
                            </p>
                            
                            <div class="bg-white p-4 rounded-2xl border border-amber-200 space-y-2 text-xs">
                                <div class="flex justify-between">
                                    <span>Denda Terlambat:</span>
                                    <span class="font-bold text-slate-800">Rp {{ number_format($return->late_fee) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Denda Kerusakan (Seller):</span>
                                    <span class="font-bold text-slate-800">Rp {{ number_format($return->damage_fee) }}</span>
                                </div>
                                <div class="flex justify-between border-t pt-2 font-bold">
                                    <span>Total Denda:</span>
                                    <span>Rp {{ number_format($return->total_fines) }}</span>
                                </div>
                                <div class="flex justify-between text-emerald-600">
                                    <span>Dipotong Deposit Jaminan:</span>
                                    <span>- Rp {{ number_format($return->deposit_amount) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3 pt-4 border-t border-amber-200/50 mt-4">
                            <p class="text-xs font-bold text-slate-700">Silakan transfer kekurangan ke Rekening Bank Toko:</p>
                            <div class="bg-white p-4 rounded-2xl border border-slate-200">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-[10px] text-slate-500 uppercase font-bold">Bank</p>
                                        <p class="text-sm font-bold text-slate-800">{{ $detail->product->store->bank_name ?? 'BCA' }}</p>
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
                        </div>
                    </div>

                    <form action="{{ route('orders.return.upload-bukti', $return->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Upload Bukti Transfer Pelunasan Denda</label>
                            <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center hover:border-emerald-400 transition bg-slate-50">
                                <input type="file" name="bukti_denda" accept="image/*" class="w-full text-sm text-slate-600" required />
                                <p class="mt-2 text-[10px] text-slate-500">Format: JPG/PNG/WEBP (maks 2MB).</p>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-2xl font-bold shadow-lg shadow-emerald-200 transition-all transform hover:-translate-y-1">
                            Kirim Bukti Pembayaran
                        </button>
                    </form>
                </div>

            <!-- ------------------ STAGE 6: DENDA SUBMITTED (MENUNGGU VERIFIKASI) ------------------ -->
            @elseif($return && $return->status === 'denda_submitted')
                <div class="text-center py-10">
                    <div class="w-20 h-20 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3 3L22 4"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Memverifikasi Bukti Denda</h3>
                    <p class="text-slate-500 max-w-md mx-auto text-sm">
                        Bukti pembayaran sisa denda Anda telah dikirim ke Penjual. Harap tunggu Penjual melakukan pencocokan mutasi rekening mereka.
                    </p>

                    @if($return->bukti_denda)
                    <div class="mt-6">
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-2 text-left">Bukti Transfer yang Anda Unggah</p>
                        <img src="{{ asset($return->bukti_denda) }}" class="w-full max-h-60 object-cover rounded-2xl shadow-sm border border-slate-200">
                    </div>
                    @endif
                </div>

            <!-- ------------------ STAGE 6.5: WAITING REFUND (MENUNGGU TRANSFER REFUND DARI ADMIN) ------------------ -->
            @elseif($return && $return->status === 'waiting_refund')
                <div class="text-center py-10">
                    <div class="w-20 h-20 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 animate-bounce text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Menunggu Refund Dana dari Admin</h3>
                    <p class="text-slate-500 max-w-md mx-auto text-sm">
                        Barang sewa telah diterima kembali oleh Penjual. Saat ini Admin sedang memproses pengembalian dana jaminan Anda sebesar <strong>Rp {{ number_format($return->to_buyer) }}</strong> ke rekening terdaftar Anda.
                    </p>
                    
                    <div class="mt-8 bg-slate-50 p-6 rounded-2xl text-left border border-slate-100 space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Detail Rekening Tujuan Refund</h4>
                        <div class="grid grid-cols-3 gap-2 text-xs">
                            <span class="text-slate-400">Bank:</span>
                            <span class="col-span-2 font-bold text-slate-800">{{ $return->buyer_refund_bank_name }}</span>
                            <span class="text-slate-400">No. Rekening:</span>
                            <span class="col-span-2 font-bold text-slate-800">{{ $return->buyer_refund_bank_account }}</span>
                            <span class="text-slate-400">Atas Nama:</span>
                            <span class="col-span-2 font-bold text-slate-800">{{ $return->buyer_refund_bank_name_owner }}</span>
                        </div>
                    </div>
                </div>

            <!-- ------------------ STAGE 7: COMPLETED (SELESAI TRANSAKSI) ------------------ -->
            @elseif($return && $return->status === 'completed')
                <div class="text-center py-10">
                    <div class="w-20 h-20 bg-emerald-500 text-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-emerald-200">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-2">Pengembalian Selesai!</h3>
                    <p class="text-slate-500 mb-8">Terima kasih telah mengembalikan alat camping dengan tertib. Transaksi sewa Anda kini dinyatakan selesai!</p>
                    
                    <div class="bg-slate-50 p-6 rounded-3xl text-left border border-slate-100">
                        <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest mb-4">Ringkasan Pengembalian</p>
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Metode</span>
                                <span class="font-bold text-slate-800">{{ $return->resi_return === 'DIANTAR_LANGSUNG' ? 'Antar Langsung (Offline)' : 'Kurir' }}</span>
                            </div>
                            @if($return->resi_return !== 'DIANTAR_LANGSUNG')
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">No. Resi</span>
                                <span class="font-bold text-slate-800">{{ $return->resi_return }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Kondisi Fisik</span>
                                <span class="font-bold text-emerald-600 uppercase">{{ $return->kondisi_barang }}</span>
                            </div>
                            
                            <div class="flex justify-between text-sm pt-3 border-t border-slate-200">
                                <span class="text-slate-500">Dana Jaminan Awal</span>
                                <span class="font-bold text-slate-900">Rp {{ number_format($return->deposit_amount) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500 text-red-500">Total Denda Sewa (-)</span>
                                <span class="font-bold text-red-600">- Rp {{ number_format($return->total_fines) }}</span>
                            </div>
                            <div class="flex justify-between items-center pt-4 mt-2 border-t-2 border-slate-200 border-dashed">
                                <span class="text-md font-black text-slate-800 uppercase tracking-widest">Dana Refund Anda</span>
                                <span class="text-2xl font-black text-emerald-600">Rp {{ number_format($return->to_buyer) }}</span>
                            </div>
                        </div>

                        <!-- Info Pencairan Keuangan Oleh Admin -->
                        <div class="mt-6 p-4 bg-emerald-50 rounded-2xl border border-emerald-100 flex gap-3 items-start">
                            <div class="shrink-0 text-emerald-600 mt-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <div class="space-y-1">
                                <h5 class="text-xs font-bold text-emerald-950">Status Pencairan Dana Jaminan</h5>
                                @if($return->refund_disbursed_at)
                                    <p class="text-[11px] text-emerald-800 leading-relaxed">
                                        Admin telah mentransfer dana refund sebesar <strong>Rp {{ number_format($return->to_buyer) }}</strong> ke rekening <strong>{{ $return->buyer_refund_bank_name }} ({{ $return->buyer_refund_bank_account }})</strong> Anda pada <strong>{{ $return->refund_disbursed_at->format('d M Y H:i') }}</strong>.
                                    </p>
                                @else
                                    <p class="text-[11px] text-slate-600 leading-relaxed">
                                        Pengembalian selesai. Admin sedang memproses transfer balik dana refund sebesar <strong>Rp {{ number_format($return->to_buyer) }}</strong> ke rekening <strong>{{ $return->buyer_refund_bank_name }} ({{ $return->buyer_refund_bank_account }})</strong> Anda.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- ------------------ LEGACY JUAL BELI VIEW Fallback ------------------ -->
            @if($return && $detail->type === 'buy')
                <div class="text-center py-10">
                    <div class="w-20 h-20 bg-emerald-500 text-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-2">Permintaan Retur Terkirim!</h3>
                    <p class="text-slate-500 mb-4">Pengajuan retur barang sedang diproses dan menunggu mediasi dari pihak Admin.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
