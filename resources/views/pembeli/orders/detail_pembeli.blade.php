@extends('layouts.app_pembeli')

@section('content')
<div class="pt-28 pb-20 bg-slate-50">
    <div class="max-w-4xl mx-auto px-4">

        <!-- HEADER -->
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-800">
                Detail Pesanan #{{ $pesanan->id }}
            </h1>

            <span class="px-4 py-1 rounded-full text-sm font-semibold
                @if($pesanan->status == 'diproses') bg-yellow-100 text-yellow-700
                @elseif($pesanan->status == 'selesai') bg-green-100 text-green-700
                @else bg-red-100 text-red-700
                @endif">
                {{ ucfirst($pesanan->status) }}
            </span>
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
                            <span class="font-bold text-slate-800">Total Transaksi</span>
                            <span class="text-xl font-black text-emerald-600">Rp {{ number_format($pesanan->total ?? 0) }}</span>
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

            <!-- PRODUK LIST -->
            <div>
                <h2 class="font-semibold text-lg mb-4">Daftar Produk</h2>

            <!-- DAFTAR ITEM -->
            <div>
                <h2 class="font-semibold text-lg mb-4 text-slate-800">Daftar Item</h2>

                <div class="space-y-6">
                    @foreach($pesanan->details as $item)
                        @php
                            $produk = $item->product;
                            $isBuy = $item->type === 'buy';
                        @endphp

                        <div class="border rounded-[24px] p-5 bg-slate-50/50 hover:bg-white hover:shadow-md transition-all duration-300">
                            <div class="flex flex-col md:flex-row gap-6">
                                <!-- FOTO PRODUK -->
                                <div class="w-full md:w-32 h-32 flex-shrink-0">
                                    <img 
                                        src="{{ $produk && $produk->image ? asset($produk->image) : 'https://via.placeholder.com/150' }}"
                                        class="w-full h-full object-cover rounded-2xl shadow-sm"
                                    >
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

                                        {{-- Rating & Review Section --}}
                                        @if($pesanan->status == 'selesai')
                                        <div class="mt-4 pt-4 border-t border-slate-200">
                                            <p class="text-sm font-bold text-slate-800 mb-3">Rating & Ulasan</p>
                                            @include('pembeli.produk.partials.review_pembeli', ['produk' => $produk])
                                        </div>
                                        @endif
                                    @else
                                        {{-- LAYOUT PENYEWAAN --}}
                                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 py-2">
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Durasi</p>
                                                <p class="font-medium">{{ $item->duration }} Hari</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Mulai Sewa</p>
                                                <p class="font-medium">{{ \Carbon\Carbon::parse($item->start_date)->format('d M Y') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Selesai Sewa</p>
                                                <p class="font-medium">{{ \Carbon\Carbon::parse($item->start_date)->addDays($item->duration)->format('d M Y') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Harga Sewa/Hari</p>
                                                <p class="font-medium text-emerald-600">Rp {{ number_format($item->harga / ($item->duration ?: 1)) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-500 uppercase font-semibold">Subtotal</p>
                                                <p class="font-bold text-slate-900">Rp {{ number_format($item->harga) }}</p>
                                            </div>
                                        </div>

                                        {{-- Return & Review Section for Rental --}}
                                        <div class="mt-4 pt-4 border-t border-slate-200 space-y-4">
                                            <div class="flex gap-3">
                                                <a href="{{ route('orders.return', $item->id) }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 transition">
                                                    Proses Pengembalian
                                                </a>
                                            </div>
                                            
                                            @if($pesanan->status == 'selesai')
                                            <div>
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
            @if(in_array($pesanan->status, ['menunggu', 'diproses']))
                <form action="{{ route('orders.cancel', $pesanan->id) }}" method="POST">
                    @csrf
                    <button class="w-full bg-red-500 hover:bg-red-600 text-white py-3 rounded-xl font-semibold transition">
                        Batalkan Pesanan
                    </button>
                </form>
            @endif

        </div>


    </div>
</div>
@endsection