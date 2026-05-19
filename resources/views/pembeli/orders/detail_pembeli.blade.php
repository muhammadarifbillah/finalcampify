@extends('layouts.app_pembeli')

@section('extra_css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    #tracking-map { height: 400px; border-radius: 24px; z-index: 10; }
    .map-marker-label { 
        background: white; 
        border: 2px solid #10B981; 
        padding: 4px 12px; 
        border-radius: 50px; 
        font-weight: 800; 
        font-size: 11px; 
        color: #064E3B;
        white-space: nowrap; 
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .package-icon {
        filter: drop-shadow(0 4px 6px rgb(0 0 0 / 0.3));
        transition: all 0.5s ease;
    }
</style>
@endsection

@section('content')
<div class="pt-28 pb-20 bg-slate-50">
    <div class="max-w-4xl mx-auto px-4">

        <!-- KYC WARNING FOR BUYER -->
        @if(!auth()->user()->ktp_verified_at)
            <div class="mb-6 p-5 bg-amber-50 border-2 border-amber-200 rounded-3xl flex gap-5 items-start">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                    <i data-lucide="shield-alert" class="w-6 h-6"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-black text-amber-900 uppercase tracking-widest text-sm mb-1">Verifikasi Identitas Diperlukan</h3>
                    <p class="text-xs text-amber-800 leading-relaxed">
                        Pesanan Anda saat ini <strong>tertahan sementara</strong> karena identitas (KTP) Anda belum diverifikasi. 
                        Penjual hanya dapat memproses pesanan setelah identitas Anda dikonfirmasi demi keamanan bersama.
                    </p>
                    <a href="{{ route('profile') }}" class="mt-3 inline-block text-xs font-bold text-amber-900 border-b-2 border-amber-900 hover:text-amber-700 transition-colors">Cek Status KTP Saya →</a>
                </div>
            </div>
        <!-- RETURN STATUS ALERTS -->
        @if($pesanan->returnRequest)
            @php
                $rStatus = $pesanan->returnRequest->status;
            @endphp

            @if($rStatus === 'dispute')
                <div class="mb-6 p-5 bg-rose-50 border-2 border-rose-200 rounded-3xl flex gap-5 items-start shadow-sm">
                    <div class="w-12 h-12 rounded-2xl bg-rose-100 flex items-center justify-center text-rose-600 shrink-0">
                        <i data-lucide="scale" class="w-6 h-6"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-black text-rose-900 uppercase tracking-widest text-sm mb-1">Sedang Mediasi Admin</h3>
                        <p class="text-xs text-rose-800 leading-relaxed">
                            Pengembalian Anda sedang ditinjau oleh tim penengah (Admin) karena adanya sengketa/ketidaksesuaian dengan penjual. 
                            Admin akan segera memberikan keputusan resolusi yang adil bagi kedua belah pihak.
                        </p>
                    </div>
                </div>
            @elseif($rStatus === 'checking')
                <div class="mb-6 p-5 bg-amber-50 border-2 border-amber-200 rounded-3xl flex gap-5 items-start shadow-sm">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                        <i data-lucide="search" class="w-6 h-6"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-black text-amber-900 uppercase tracking-widest text-sm mb-1">Pemeriksaan Barang</h3>
                        <p class="text-xs text-amber-800 leading-relaxed">
                            Barang yang Anda kembalikan sedang diperiksa oleh penjual/admin untuk memastikan kondisinya. 
                            Status akan diperbarui segera setelah pemeriksaan selesai.
                        </p>
                    </div>
                </div>
            @elseif($rStatus === 'pending')
                <div class="mb-6 p-5 bg-blue-50 border-2 border-blue-200 rounded-3xl flex gap-5 items-start shadow-sm">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                        <i data-lucide="clock" class="w-6 h-6"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-black text-blue-900 uppercase tracking-widest text-sm mb-1">Menunggu Penjual</h3>
                        <p class="text-xs text-blue-800 leading-relaxed">
                            Permintaan pengembalian Anda telah dikirim. Menunggu konfirmasi atau respon dari pihak penjual.
                        </p>
                    </div>
                </div>
            @endif
        @endif

        <!-- HEADER -->
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-800">
                {{ isset($detailId) ? 'Detail Barang' : 'Detail Pesanan' }} #{{ $pesanan->id }}
            </h1>

            @php
                // Jika sedang melihat barang spesifik, ambil info rental khusus barang itu
                if (isset($detailId)) {
                    $item = $pesanan->details->first();
                    $rental = \App\Models\Pembeli\Rental_pembeli::where('order_id', $pesanan->id)
                                ->where('product_id', $item->product_id)
                                ->first();
                } else {
                    $rental = \App\Models\Pembeli\Rental_pembeli::where('order_id', $pesanan->id)->first();
                }

                $displayStatus = $pesanan->status;
                $statusColor = 'bg-amber-100 text-amber-700';

                if ($rental) {
                    $displayStatus = $rental->status;
                    $statusColor = match($rental->status) {
                        'active' => 'bg-blue-100 text-blue-700',
                        'returned' => 'bg-indigo-100 text-indigo-700',
                        'completed' => 'bg-emerald-100 text-emerald-700',
                        'cancelled' => 'bg-red-100 text-red-700',
                        default => 'bg-amber-100 text-amber-700'
                    };
                } else {
                    $statusColor = match($pesanan->status) {
                        'diproses' => 'bg-yellow-100 text-yellow-700',
                        'selesai' => 'bg-green-100 text-green-700',
                        'menunggu' => 'bg-amber-100 text-amber-700',
                        'dibatalkan' => 'bg-red-100 text-red-700',
                        default => 'bg-slate-100 text-slate-700'
                    };
                }
            @endphp

            <div class="flex items-center gap-2">
                @if(isset($detailId))
                    @php $specificItem = $pesanan->details->first(); @endphp
                    <span class="px-3 py-1 {{ $specificItem->type === 'rent' ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100' }} rounded-full text-[10px] font-black uppercase tracking-widest border">
                        {{ $specificItem->type === 'rent' ? 'Penyewaan' : 'Pembelian' }}
                    </span>
                @elseif($rental)
                    <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-blue-100">Penyewaan</span>
                @endif
                <span class="px-4 py-1 rounded-full text-sm font-bold uppercase {{ $statusColor }}">
                    {{ ucfirst($displayStatus) }}
                </span>
            </div>
        </div>

        <!-- CARD -->
        <div class="bg-white rounded-3xl shadow-lg p-6 space-y-6">

            <!-- INFO PENGIRIMAN & PEMBAYARAN -->
            <div class="grid md:grid-cols-2 gap-8 border-b pb-6">
                <div class="space-y-3">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Informasi Pengiriman</p>
                    <div class="space-y-1">
                        <p class="font-bold text-slate-800">{{ $pesanan->receiver_name }}</p>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            {{ $pesanan->shipping_address }}<br>
                            {{ $pesanan->shipping_district }}, {{ $pesanan->shipping_city }}<br>
                            {{ $pesanan->shipping_postal_code }}
                        </p>
                        <p class="text-sm text-slate-600 mt-2">
                            <span class="font-semibold text-slate-800">Telepon:</span> {{ $pesanan->shipping_phone }}
                        </p>
                    </div>
                    <div class="pt-2">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-slate-100 rounded-lg">
                            <span class="text-[10px] font-bold text-slate-500 uppercase">Kurir:</span>
                            <span class="text-xs font-bold text-slate-800 uppercase">{{ $pesanan->kurir ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Rincian Pembayaran</p>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">Metode Pembayaran</span>
                            <span class="font-bold text-slate-800 uppercase">{{ $pesanan->metode_pembayaran }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">Status Pembayaran</span>
                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded text-[10px] font-bold uppercase">Lunas</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t">
                            <span class="font-bold text-slate-800">Total Transaksi {{ isset($detailId) ? '(Barang Ini)' : '' }}</span>
                            <span class="text-xl font-black text-emerald-600">
                                Rp {{ number_format(isset($detailId) ? ($pesanan->details->first()->harga * $pesanan->details->first()->qty + ($pesanan->details->first()->type === 'rent' ? ($pesanan->details->first()->product->buy_price * 0.25) : 0)) : ($pesanan->total ?? 0)) }}
                            </span>
                        </div>
                        @if($pesanan->bukti_pembayaran)
                        <div class="pt-4 mt-4 border-t border-dashed border-slate-200">
                            <p class="text-[10px] font-bold text-slate-400 uppercase mb-2">Bukti Pembayaran</p>
                            <a href="{{ asset($pesanan->bukti_pembayaran) }}" target="_blank" class="group block relative rounded-xl overflow-hidden border-2 border-slate-100 hover:border-emerald-500 transition-all">
                                <img src="{{ asset($pesanan->bukti_pembayaran) }}" class="w-full h-32 object-cover opacity-80 group-hover:opacity-100 transition-opacity">
                                <div class="absolute inset-0 flex items-center justify-center bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="text-white text-xs font-bold px-3 py-1 bg-black/50 rounded-full">Lihat Full Image</span>
                                </div>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- MAP TRACKING SECTION -->
            <div class="pt-6 border-b pb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Lacak Posisi Paket</p>
                        <h2 class="font-bold text-slate-800">Visual Maps Tracking</h2>
                    </div>
                    @if($pesanan->status == 'dikirim')
                        <span class="flex items-center gap-2 text-xs font-bold text-emerald-600 animate-pulse">
                            <span class="w-2 h-2 bg-emerald-600 rounded-full"></span> Live Tracking Active
                        </span>
                    @endif
                </div>
                
                <div class="relative group">
                    <div id="tracking-map" class="shadow-inner border border-slate-200"></div>
                    <div class="absolute bottom-4 left-4 right-4 z-[1000] flex gap-2">
                        <div class="bg-white/90 backdrop-blur px-3 py-2 rounded-xl shadow-sm border border-slate-200 flex items-center gap-3">
                            <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600">
                                🚚
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase leading-none mb-1">Status Kurir</p>
                                <p class="text-xs font-bold text-slate-800">
                                    @if($pesanan->status == 'diproses') Pesanan sedang dikemas
                                    @elseif($pesanan->status == 'dikirim') Sedang dalam perjalanan ke lokasi Anda
                                    @elseif($pesanan->status == 'selesai') Paket telah diterima
                                    @else Menunggu konfirmasi pembayaran
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DAFTAR ITEM -->
            <div>
                <h2 class="font-semibold text-lg mb-4 text-slate-800">Daftar Item</h2>

                <div class="space-y-6">
                    @foreach($pesanan->details as $item)
                        @php
                            $produk = $item->product;
                            // Cek tipe: jika di DB 'buy' tapi nama ada '(Sewa)', anggap sewa agar tampilan benar
                            $isBuy = ($item->type === 'buy' && !str_contains(strtolower($produk->name ?? $produk->nama_produk), '(sewa)'));
                        @endphp

                        <div class="border rounded-[24px] p-5 bg-slate-50/50 hover:bg-white hover:shadow-md transition-all duration-300">
                            <div class="flex flex-col md:flex-row gap-6">
                                <!-- FOTO PRODUK -->
                                <div class="w-full md:w-32 h-32 flex-shrink-0">
                                    @if($produk->image_url)
                                        <img src="{{ $produk->image_url }}" class="w-full h-full object-cover rounded-2xl shadow-sm" alt="{{ $produk->name ?? $produk->nama_produk }}">
                                    @else
                                        <div class="w-full h-full bg-slate-100 rounded-2xl flex items-center justify-center text-3xl">📦</div>
                                    @endif
                                </div>

                                <!-- INFO UTAMA -->
                                <div class="flex-1 space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                            bg-emerald-100 text-emerald-700">
                                            {{ $isBuy ? 'Pembelian' : 'Penyewaan' }}
                                        </span>
                                        <h3 class="font-bold text-slate-900 text-lg">
                                            {{ $produk->name ?? 'Produk tidak ditemukan' }}
                                        </h3>
                                    </div>

                                    @if($isBuy)
                                        {{-- LAYOUT PEMBELIAN --}}
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 py-2">
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Jumlah</p>
                                                <p class="font-medium">{{ $item->qty }} Item</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Harga Satuan</p>
                                                <p class="font-medium text-emerald-600">Rp {{ number_format($item->harga) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Nomor Resi</p>
                                                <p class="font-medium text-emerald-600">{{ $pesanan->no_resi ?? 'Menunggu Pengiriman' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Subtotal</p>
                                                <p class="font-bold text-slate-900">Rp {{ number_format($item->harga * $item->qty) }}</p>
                                            </div>
                                        </div>

                                        {{-- Retur Section for Buy --}}
                                        @php
                                            $buyReturn = \App\Models\Pembeli\Return_pembeli::where('order_id', $pesanan->id)
                                                ->where('type', 'jual_beli')
                                                ->first();
                                        @endphp
                                        <div class="mt-4 pt-4 border-t border-slate-200">
                                            @if(!$buyReturn)
                                                @if($pesanan->status === 'selesai')
                                                    <a href="{{ route('orders.return', $item->id) }}" class="inline-flex items-center justify-center rounded-xl border-2 border-red-200 px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-50 transition">
                                                        <i data-lucide="refresh-ccw" class="w-3 h-3 me-2"></i> AJUKAN RETUR BARANG
                                                    </a>
                                                @endif
                                            @else
                                                <div class="p-4 bg-red-50 rounded-2xl border border-red-100 flex items-center justify-between">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
                                                        <span class="text-[10px] font-black text-red-700 uppercase tracking-widest">Status Retur: {{ $buyReturn->status }}</span>
                                                    </div>
                                                    <a href="{{ route('orders.return', $item->id) }}" class="text-[10px] font-bold text-red-900 underline">Lihat Detail</a>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Rating & Review Section --}}
                                        @if($pesanan->status == 'selesai')
                                        <div class="mt-4 pt-4 border-t border-slate-200" id="review-section">
                                            <p class="text-sm font-bold text-slate-800 mb-3">Rating & Ulasan</p>
                                            @include('pembeli.produk.partials.review_pembeli', ['produk' => $produk])
                                        </div>
                                        @endif
                                    @else
                                        {{-- LAYOUT PENYEWAAN --}}
                                        @php
                                            $returnInfo = \App\Models\Pembeli\Return_pembeli::where('order_id', $pesanan->id)->where('type', 'sewa')->first();
                                            $rentalInfo = \App\Models\Pembeli\Rental_pembeli::where('order_id', $pesanan->id)->first();
                                            $canReturnRental = in_array($pesanan->status, ['selesai', 'sampai']) || ($rentalInfo && in_array($rentalInfo->status, ['active', 'selesai', 'completed']));
                                        @endphp
                                        
                                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 py-2">
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Durasi</p>
                                                <p class="font-medium">{{ $item->duration ?? '3' }} Hari</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Mulai Sewa</p>
                                                <p class="font-medium">{{ $item->start_date ? \Carbon\Carbon::parse($item->start_date)->format('d M Y') : \Carbon\Carbon::parse($pesanan->created_at)->format('d M Y') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Selesai Sewa</p>
                                                <p class="font-medium">{{ $item->start_date ? \Carbon\Carbon::parse($item->start_date)->addDays($item->duration ?? 3)->format('d M Y') : \Carbon\Carbon::parse($pesanan->created_at)->addDays(3)->format('d M Y') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Harga Sewa</p>
                                                <p class="font-medium text-emerald-600">Rp {{ number_format($item->harga) }}</p>
                                            </div>
                                            <div>
                                                @php
                                                    // Ambil deposit dari record return jika sudah ada, atau hitung manual
                                                    $deposit = ($returnInfo && $returnInfo->deposit_amount > 0) 
                                                        ? $returnInfo->deposit_amount 
                                                        : (($produk->buy_price ?? 0) * 0.25);
                                                    
                                                    // Jika tetap 0 tapi ada escrow_total di return, gunakan itu
                                                    if ($deposit <= 0 && $returnInfo && $returnInfo->escrow_total > 0) {
                                                        $deposit = $returnInfo->escrow_total;
                                                    }
                                                @endphp
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Dana Jaminan (Escrow)</p>
                                                <p class="font-bold text-blue-600">Rp {{ number_format($deposit) }}</p>
                                                <p class="text-[8px] text-slate-400 mt-0.5 leading-tight">Akan dikembalikan utuh jika barang kembali aman.</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Total Dibayar</p>
                                                <p class="font-bold text-slate-900">Rp {{ number_format($item->harga + $deposit) }}</p>
                                            </div>
                                        </div>

                                        {{-- Return & Review Section for Rental --}}
                                        <div class="mt-4 pt-4 border-t border-slate-200 space-y-4">
                                            <div class="flex flex-col gap-3">
                                                @if(!$returnInfo)
                                                    @if($canReturnRental)
                                                        <a href="{{ route('orders.return', $item->id) }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 transition shadow-lg shadow-slate-200">
                                                            <i data-lucide="rotate-ccw" class="w-3 h-3 me-2"></i> AJUKAN PENGEMBALIAN SEKARANG
                                                        </a>
                                                    @elseif($rentalInfo && $rentalInfo->status === 'completed')
                                                        <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100 flex items-center gap-3">
                                                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                                                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                                            </div>
                                                            <p class="text-[10px] text-emerald-800 font-bold uppercase tracking-widest">Penyewaan Selesai</p>
                                                        </div>
                                                    @else
                                                        <div class="p-4 bg-slate-100 rounded-2xl border border-slate-200">
                                                            <p class="text-[10px] text-slate-500 font-bold leading-relaxed uppercase tracking-widest text-center">
                                                                <i data-lucide="clock" class="w-3 h-3 inline me-1"></i> Form pengembalian muncul setelah status "AKTIF"
                                                            </p>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-3">
                                                        <div class="flex items-center justify-between">
                                                            <div class="flex items-center gap-2">
                                                                <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
                                                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Status Pengembalian</span>
                                                            </div>
                                                            <span class="px-2 py-0.5 {{ $returnInfo->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-indigo-100 text-indigo-700' }} rounded text-[9px] font-bold uppercase">{{ $returnInfo->status }}</span>
                                                        </div>
                                                        
                                                        @if($returnInfo->status === 'completed')
                                                            <div class="space-y-2 pt-2 border-t border-dashed border-slate-200">
                                                                <div class="flex justify-between text-xs">
                                                                    <span class="text-slate-500">Dana Jaminan Awal</span>
                                                                    <span class="font-bold text-slate-800">Rp {{ number_format($returnInfo->deposit_amount) }}</span>
                                                                </div>
                                                                <div class="flex justify-between text-xs">
                                                                    <span class="text-slate-500">Total Denda (Telat + Rusak)</span>
                                                                    <span class="font-bold text-red-600">- Rp {{ number_format($returnInfo->total_fines) }}</span>
                                                                </div>
                                                                <div class="flex justify-between items-center pt-2 border-t border-slate-200">
                                                                    <span class="text-sm font-bold text-emerald-700">Refund Cair ke Anda</span>
                                                                    <span class="text-lg font-black text-emerald-600">Rp {{ number_format($returnInfo->to_buyer) }}</span>
                                                                </div>
                                                                <p class="text-[9px] text-slate-400 text-center italic mt-2">Dana telah dikembalikan ke saldo/rekening Anda.</p>
                                                            </div>
                                                        @else
                                                            <p class="text-[10px] text-slate-500 italic">Barang dalam proses pengecekan oleh Admin & Penjual. Harap tunggu konfirmasi refund.</p>
                                                        @endif
                                                        <a href="{{ route('orders.return', $item->id) }}" class="mt-2 w-full inline-flex items-center justify-center rounded-xl border-2 border-slate-200 py-2 text-[10px] font-black text-slate-600 hover:bg-slate-50 transition uppercase tracking-widest">
                                                            Lihat Detail Pengembalian →
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            @if($pesanan->status == 'selesai')
                                            <div id="review-section">
                                                <p class="text-sm font-bold text-slate-800 mb-3">Rating & Ulasan Penyewaan</p>
                                                @include('pembeli.produk.partials.review_pembeli', ['produk' => $produk])
                                            </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- REVIEW TOKO -->
            @if($pesanan->status == 'selesai' && $pesanan->details->isNotEmpty())
                @php
                    $firstItem = $pesanan->details->first();
                    // Gunakan properti yang sesuai untuk mendapat store_id/seller_id, contoh sellerUserId()
                    $storeId = $firstItem->product->sellerUserId() ?? $firstItem->product->seller_id ?? $firstItem->product->user_id;
                    
                    $existingStoreReview = \App\Models\Pembeli\StoreRating_pembeli::where('user_id', \Illuminate\Support\Facades\Auth::id())
                        ->where('store_id', $storeId)
                        ->first();
                @endphp

                <div class="mt-8 pt-6 border-t border-slate-200">
                    <h2 class="font-semibold text-lg mb-4 text-slate-800">Rating & Ulasan Toko</h2>
                    @if($existingStoreReview)
                        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <div class="flex text-yellow-400 text-lg">
                                        @for($i=1; $i<=5; $i++)
                                            <span>{!! $i <= $existingStoreReview->rating ? '★' : '☆' !!}</span>
                                        @endfor
                                    </div>
                                    <span class="text-xs font-bold text-slate-400">{{ $existingStoreReview->created_at->format('d M Y') }}</span>
                                </div>
                                <p class="text-sm text-slate-700 font-medium">"{{ $existingStoreReview->comment }}"</p>
                            </div>
                        </div>
                    @else
                        <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200">
                            <form action="{{ route('store.review.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $pesanan->id }}">
                                <input type="hidden" name="store_id" value="{{ $storeId }}">
                                <input type="hidden" name="rating" id="store-rating-value" value="5">
                                
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-3">Berikan Rating Untuk Toko</label>
                                    <div class="flex gap-2 text-3xl" id="store-star-container">
                                        @for($i=1; $i<=5; $i++)
                                            <button type="button" 
                                                    onclick="setStoreRating({{ $i }})" 
                                                    class="store-star-btn text-yellow-400 transition-transform hover:scale-110 focus:outline-none"
                                                    data-value="{{ $i }}">
                                                ★
                                            </button>
                                        @endfor
                                    </div>
                                </div>
                                
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-slate-500 uppercase">Tulis Ulasan Toko</label>
                                    <textarea name="comment" rows="3" class="w-full rounded-2xl border-slate-200 text-sm focus:ring-emerald-500 focus:border-emerald-500" placeholder="Bagaimana pelayanan dari toko ini?" required></textarea>
                                </div>
                                
                                <button type="submit" class="w-full md:w-auto bg-emerald-600 text-white px-8 py-3 rounded-xl text-sm font-bold hover:bg-emerald-700 shadow-lg shadow-emerald-100 transition-all">
                                    Kirim Ulasan Toko
                                </button>
                            </form>

                            <script>
                                function setStoreRating(value) {
                                    document.getElementById('store-rating-value').value = value;
                                    const stars = document.querySelectorAll('.store-star-btn');
                                    stars.forEach((star, index) => {
                                        if (index < value) {
                                            star.innerHTML = '★';
                                            star.classList.remove('text-slate-300');
                                            star.classList.add('text-yellow-400');
                                        } else {
                                            star.innerHTML = '☆';
                                            star.classList.remove('text-yellow-400');
                                            star.classList.add('text-slate-300');
                                        }
                                    });
                                }
                            </script>
                        </div>
                    @endif
                </div>
            @endif
            </div>

            <!-- ACTION -->
            <div class="flex flex-col gap-3">
                @if($pesanan->status == 'dikirim')
                    <form action="{{ route('orders.confirm', $pesanan->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-2xl font-black text-sm uppercase tracking-widest transition-all shadow-lg shadow-emerald-100 flex items-center justify-center gap-3">
                            <i data-lucide="package-check" class="w-5 h-5"></i>
                            Konfirmasi Pesanan Diterima
                        </button>
                    </form>
                @endif

                @if(in_array($pesanan->status, ['menunggu', 'diproses']))
                    <form action="{{ route('orders.cancel', $pesanan->id) }}" method="POST">
                        @csrf
                        <button class="w-full bg-red-50 hover:bg-red-100 text-red-600 py-3 rounded-xl font-bold text-xs uppercase tracking-widest transition border border-red-100">
                            Batalkan Pesanan
                        </button>
                    </form>
                @endif
            </div>

        </div>


    </div>
</div>
@endsection

@section('extra_js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data dari PHP
        let storeLat = {{ $pesanan->details->first()->product->store->latitude ?? 'null' }};
        let storeLng = {{ $pesanan->details->first()->product->store->longitude ?? 'null' }};
        let destLat = {{ $pesanan->latitude ?? 'null' }};
        let destLng = {{ $pesanan->longitude ?? 'null' }};
        
        const storeAddr = "{{ $pesanan->details->first()->product->store->alamat ?? '' }}";
        const buyerAddr = "{{ $pesanan->shipping_address }}, {{ $pesanan->shipping_district }}, {{ $pesanan->shipping_city }}";
        const status = "{{ $pesanan->status }}";

        // Inisialisasi Map (Default ke Jakarta jika benar-benar kosong)
        const map = L.map('tracking-map').setView([storeLat || -6.1754, storeLng || 106.8272], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Custom Icons
        const storeIcon = L.divIcon({ 
            className: 'custom-div-icon', 
            html: '<div class="map-marker-label"><span>🏪</span> Toko</div>', 
            iconSize: [null, null], 
            iconAnchor: [35, 15] 
        });
        const buyerIcon = L.divIcon({ 
            className: 'custom-div-icon', 
            html: '<div class="map-marker-label"><span>🏠</span> Anda</div>', 
            iconSize: [null, null], 
            iconAnchor: [35, 15] 
        });
        const packageIcon = L.divIcon({
            html: '<div style="font-size: 32px;">🚚</div>',
            className: 'package-icon',
            iconSize: [40, 40],
            iconAnchor: [20, 20]
        });

        let storeMarker, buyerMarker, path, packageMarker;

        async function geocode(address) {
            if (!address) return null;
            try {
                // Percobaan 1: Alamat Lengkap
                let response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`);
                let data = await response.json();
                
                // Percobaan 2: Jika gagal, coba ambil bagian kota/kecamatan saja
                if (data.length === 0) {
                    const parts = address.split(',');
                    if (parts.length > 1) {
                        const simplified = parts.slice(-2).join(',').trim();
                        response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(simplified)}&limit=1`);
                        data = await response.json();
                    }
                }

                if (data.length > 0) {
                    return { lat: parseFloat(data[0].lat), lon: parseFloat(data[0].lon) };
                }
            } catch (e) { console.error("Geocoding error:", e); }
            return null;
        }

        async function initTracking() {
            // Jika koordinat kosong, cari otomatis
            if (!storeLat || !storeLng) {
                const geo = await geocode(storeAddr);
                if (geo) { storeLat = geo.lat; storeLng = geo.lon; }
            }
            if (!destLat || !destLng) {
                const geo = await geocode(buyerAddr);
                if (geo) { destLat = geo.lat; destLng = geo.lon; }
            }

            // Gunakan fallback akhir jika tetap gagal
            storeLat = storeLat || -6.1754;
            storeLng = storeLng || 106.8272;
            destLat = destLat || -6.2088;
            destLng = destLng || 106.8456;

            // Tambahkan Markers
            storeMarker = L.marker([storeLat, storeLng], {icon: storeIcon}).addTo(map);
            buyerMarker = L.marker([destLat, destLng], {icon: buyerIcon}).addTo(map);

            // Tambahkan Jalur
            path = L.polyline([[storeLat, storeLng], [destLat, destLng]], {
                color: '#10B981',
                weight: 4,
                dashArray: '10, 15',
                opacity: 0.5
            }).addTo(map);

            // Tentukan Posisi Paket
            let packagePos = [storeLat, storeLng];
            if (status === 'dikirim') {
                packagePos = [(storeLat + destLat) / 2, (storeLng + destLng) / 2];
            } else if (status === 'selesai') {
                packagePos = [destLat, destLng];
            }

            packageMarker = L.marker(packagePos, {icon: packageIcon}).addTo(map);
            
            // Zoom ke area rute
            map.fitBounds(path.getBounds(), {padding: [70, 70]});
        }

        initTracking();
    });
</script>
@endsection
