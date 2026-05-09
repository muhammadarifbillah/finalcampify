@extends('layouts.app_pembeli')

@section('content')
<div class="pt-28 pb-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4">

        <div class="mb-6 flex items-center justify-between gap-4">
            <a href="{{ route('produk.index') }}" class="text-sm text-green-600 hover:text-green-800 font-semibold">← Kembali ke Produk</a>
            <div class="flex items-center gap-3 text-sm text-gray-500">
                <span>{{ $produk->category }}</span>
                <span>•</span>
                <span>{{ $produk->stock > 0 ? 'Tersedia' : 'Habis' }}</span>
            </div>
        </div>

        <div class="grid gap-10 xl:grid-cols-[1.5fr_1fr]">

            <div class="space-y-6">
                <div class="rounded-[32px] overflow-hidden shadow-lg bg-white">
                    <img src="{{ $produk->image ? asset('storage/' . $produk->image) : 'https://images.unsplash.com/photo-1535745665858-41619828c30f' }}"
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
                    <div class="flex items-center justify-between gap-4 mb-6">
                        <div>
                            <div class="flex items-center gap-3">
                                <div>
                                    <p class="text-sm uppercase tracking-[0.2em] text-emerald-600 font-semibold mb-2">
                                        {{ ($mode ?? 'buy') === 'rent' ? 'Detail Penyewaan' : 'Detail Pembelian' }}
                                    </p>
                                    <h1 class="text-3xl font-bold text-slate-900">{{ $produk->name }}</h1>
                                </div>
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



                    <div class="grid gap-4 mb-6">
                        <div class="rounded-3xl bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Harga Beli</p>
                            <p class="text-2xl font-bold text-emerald-700">Rp {{ number_format($produk->buy_price) }}</p>
                        </div>
                        <div class="rounded-3xl bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Harga Sewa</p>
                            @if($produk->rent_price && $produk->rent_price > 0)
                                <p class="text-2xl font-bold text-blue-700">Rp {{ number_format($produk->rent_price) }}/hari</p>
                            @else
                                <p class="text-sm text-slate-400 italic">Tidak tersedia untuk sewa</p>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-6">
                        @php $mode = $mode ?? 'buy'; @endphp

                        @if($mode === 'buy')
                            <div class="bg-slate-50 rounded-3xl p-5 border border-slate-200">
                                <h3 class="text-lg font-semibold text-slate-900 mb-4">Beli Sekarang</h3>
                                <form action="{{ route('cart.add') }}" method="POST" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $produk->id }}">
                                    <input type="hidden" name="type" value="buy">

                                    <div>
                                        <p class="text-sm text-slate-500">Jumlah Pembelian</p>
                                        <input type="number" name="quantity" min="1" max="{{ max(1, $produk->stock) }}" value="1" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" />
                                    </div>

                                    <div class="grid gap-3 sm:grid-cols-[1fr_auto]">
                                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800 w-full">Tambah Keranjang</button>
                                        <a href="{{ route('checkout.now', $produk->id) }}" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-700">Beli Sekarang</a>
                                    </div>
                                </form>
                            </div>

                            @if($produk->rent_price && $produk->rent_price > 0)
                                <div class="bg-blue-50 rounded-3xl p-5 border border-blue-200">
                                    <p class="text-sm text-slate-500 mb-3">Atau pilih penyewaan jika kamu butuh sementara.</p>
                                    <a href="{{ route('produk.detail.rent', $produk->id) }}" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">Lihat Detail Sewa</a>
                                </div>
                            @endif
                        @else
                            <div class="bg-slate-50 rounded-3xl p-5 border border-slate-200">
                                <h3 class="text-lg font-semibold text-slate-900 mb-4">Sewa Sekarang</h3>
                                <form action="{{ route('cart.add') }}" method="POST" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $produk->id }}">
                                    <input type="hidden" name="type" value="rent">

                                    <div>
                                        <p class="text-sm text-slate-500">Tanggal Peminjaman</p>
                                        <input type="date" name="start_date" min="{{ date('Y-m-d') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" />
                                    </div>
                                    <div>
                                        <p class="text-sm text-slate-500">Durasi Sewa (hari)</p>
                                        <input type="number" name="duration" min="1" value="1" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3" />
                                    </div>

                                    <div class="grid gap-3 sm:grid-cols-[1fr_auto]">
                                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800 w-full">Tambah Keranjang</button>
                                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">Sewa Sekarang</button>
                                    </div>
                                </form>
                            </div>

                            @if($produk->buy_price && $produk->buy_price > 0)
                                <div class="bg-emerald-50 rounded-3xl p-5 border border-emerald-200">
                                    <p class="text-sm text-slate-500 mb-3">Atau pilih pembelian jika kamu ingin memiliki barang ini.</p>
                                    <a href="{{ route('produk.detail.buy', $produk->id) }}" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-700">Lihat Detail Beli</a>
                                </div>
                            @endif
                        @endif
                    </div>

                    <div class="mt-8 pt-8 border-t border-slate-100">
                        @auth
                            @if(auth()->user()->role === 'buyer')
                                <form method="POST" action="{{ route('products.report', $produk->id) }}" class="mb-4 grid gap-2 sm:grid-cols-[160px_1fr_auto]">
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
                                    <form method="POST" action="{{ route('stores.report', $produk->store_id) }}" class="grid gap-2 sm:grid-cols-[160px_1fr_auto]">
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
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
