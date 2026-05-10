@extends('layouts.app_pembeli')

@section('content')
<div class="pt-28 pb-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4">

        <div class="mb-6 flex items-center justify-between gap-4">
            <a href="{{ route('produk.rental') }}" class="text-sm text-green-600 hover:text-green-800 font-semibold">← Kembali ke Produk Sewa</a>
            <div class="flex items-center gap-3 text-sm text-gray-500">
                <span>{{ $produk->category }}</span>
                <span>•</span>
                <span>{{ $produk->stock > 0 ? 'Tersedia' : 'Habis' }}</span>
            </div>
        </div>

        <div class="grid gap-10 xl:grid-cols-[1.5fr_1fr]">

            <div class="space-y-6">
                <div class="rounded-[32px] overflow-hidden shadow-lg bg-white">
                    <img src="{{ asset($produk->image) }}"
                         alt="{{ $produk->name }}"
                         class="w-full h-[520px] object-cover object-center">
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="bg-white rounded-3xl p-6 shadow-sm">
                        <h2 class="text-xl font-bold mb-4">Detail Produk</h2>
                        <p class="text-sm leading-7 text-slate-600">{{ $produk->description }}</p>
                    </div>
                    <div class="bg-white rounded-3xl p-6 shadow-sm">
                        <h2 class="text-xl font-bold mb-4">Rating & Review</h2>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex text-yellow-400 text-xl">
                                @for($i=1; $i<=5; $i++)
                                    @if($i <= $produk->rating)
                                        <span>★</span>
                                    @else
                                        <span class="text-slate-300">★</span>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-sm text-slate-500">({{ $produk->reviews_count }} ulasan)</span>
                        </div>
                        <p class="text-6xl font-bold text-slate-900">{{ number_format($produk->rating, 1) }}</p>
                    </div>
                </div>
            </div>

            <aside class="space-y-6">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                    <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-[0.2em] mb-2">Detail Penyewaan</p>
                    <h1 class="text-3xl font-black text-slate-900 leading-tight mb-2">{{ $produk->name }}</h1>
                    <div class="flex items-center justify-between gap-4 mb-6">
                        <div>
                            <div class="flex items-center gap-3">
                                @auth
                                    <form action="{{ route('wishlist.toggle') }}" method="POST" class="inline-flex items-center">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $produk->id }}">
                                        <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 transition duration-200
                                            {{ in_array($produk->id, $wishlistProductIds ?? []) ? 'bg-red-100 text-red-600 shadow-sm' : 'text-slate-500 hover:bg-slate-100' }}"
                                                title="Tambah ke favorit">
                                            {{ in_array($produk->id, $wishlistProductIds ?? []) ? '❤️' : '🤍' }}
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 text-slate-500 hover:bg-slate-100" title="Login untuk favorit">
                                        🤍
                                    </a>
                                @endauth
                            </div>
                            <p class="text-sm text-slate-500 mt-1">Penjual: {{ $produk->store?->nama_toko ?? $produk->seller?->name ?? $produk->owner?->name ?? 'Toko Resmi' }}</p>
                        </div>
                        <a href="{{ route('chat.product.start', $produk->id) }}" class="inline-flex h-12 w-12 items-center justify-center rounded-2xl border border-slate-200 text-slate-600 hover:bg-slate-100" title="Hubungi Penjual">
                            💬
                        </a>
                    </div>

                    @auth
                        @if(auth()->user()->role === 'buyer')
                            <form method="POST" action="{{ route('products.report', $produk->id) }}" class="mb-6 grid gap-2 sm:grid-cols-[160px_1fr_auto]">
                                @csrf
                                <select name="reason" class="rounded-2xl border border-slate-200 px-3 py-2 text-sm">
                                    <option value="Produk mencurigakan">Produk mencurigakan</option>
                                    <option value="Harga tidak wajar">Harga tidak wajar</option>
                                    <option value="Deskripsi menyesatkan">Deskripsi menyesatkan</option>
                                </select>
                                <input name="description" class="rounded-2xl border border-slate-200 px-3 py-2 text-sm" placeholder="Detail laporan (opsional)">
                                <button class="rounded-2xl border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50">Laporkan</button>
                            </form>
                        @endif
                    @endauth

                    @if($produk->store_id)
                        @auth
                            @if(auth()->user()->role === 'buyer')
                                <form method="POST" action="{{ route('stores.report', $produk->store_id) }}" class="mb-6 grid gap-2 sm:grid-cols-[160px_1fr_auto]">
                                    @csrf
                                    <select name="reason" class="rounded-2xl border border-slate-200 px-3 py-2 text-sm">
                                        <option value="Toko mencurigakan">Toko mencurigakan</option>
                                        <option value="Pelayanan buruk">Pelayanan buruk</option>
                                        <option value="Informasi toko tidak valid">Informasi toko tidak valid</option>
                                    </select>
                                    <input name="description" class="rounded-2xl border border-slate-200 px-3 py-2 text-sm" placeholder="Detail laporan toko (opsional)">
                                    <button class="rounded-2xl border border-orange-200 px-4 py-2 text-sm font-semibold text-orange-600 hover:bg-orange-50">Laporkan Toko</button>
                                </form>
                            @endif
                        @endauth
                    @endif

                    <div class="grid gap-4 mb-6">
                        <div class="rounded-3xl bg-slate-50 p-4">
                            <p class="text-sm text-slate-500 uppercase font-bold tracking-wider mb-1">Harga Sewa</p>
                            <p class="text-3xl font-black text-emerald-600">Rp {{ number_format($produk->rent_price) }}<span class="text-sm font-bold text-slate-400">/hari</span></p>
                        </div>
                        <div class="rounded-3xl bg-blue-50 p-4 border border-blue-100">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[10px] text-blue-600 uppercase font-black tracking-wider">Dana Jaminan (Keamanan)</span>
                                <div class="group relative">
                                    <span class="cursor-help text-blue-400 text-xs">ⓘ</span>
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-48 p-2 bg-slate-900 text-white text-[10px] rounded-lg shadow-xl z-20">
                                        Dana ini adalah deposit 50% dari harga beli produk (Rp {{ number_format($produk->buy_price) }}) yang akan dikembalikan utuh jika barang kembali aman.
                                    </div>
                                </div>
                            </div>
                            <p class="text-xl font-black text-blue-700">Rp {{ number_format($produk->buy_price * 0.5) }}</p>
                            <p class="text-[9px] text-blue-500 italic mt-1">*Dibayarkan sekali di awal, kembali utuh di akhir.</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-slate-50 rounded-3xl p-5 border border-slate-200">
                            <h3 class="text-lg font-semibold text-slate-900 mb-4">Sewa Sekarang</h3>
                            <form action="{{ route('sewa.form', $produk->id) }}" method="GET" class="space-y-4">
                                <p class="text-sm text-slate-500 mb-3 leading-relaxed">Klik tombol di bawah untuk mengisi formulir penyewaan. <strong>Catatan:</strong> Anda wajib mengunggah foto KTP untuk keamanan transaksi.</p>
                                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-4 text-base font-bold text-white hover:bg-emerald-700 w-full shadow-lg shadow-emerald-100 transition-all">Lanjutkan ke Formulir Sewa</button>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
