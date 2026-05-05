@extends('layouts.admin')

@section('content')

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Validasi Produk</h1>
            <p class="text-gray-600">Kelola produk baru dan tautkan ke kurir yang tersedia.</p>
        </div>
       
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 border border-green-200 p-4 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div id="productModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40 p-4">
        <div class="w-full max-w-2xl rounded-[32px] bg-white p-6 shadow-2xl max-h-[calc(100vh-3rem)] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-semibold">Tambah Produk</h2>
                <button type="button" id="closeProductModal" class="text-gray-500 hover:text-gray-900">Tutup</button>
            </div>
            <form method="POST" action="/admin/products/store" class="space-y-4">
                @csrf
                <div class="grid gap-4 lg:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Produk</label>
                        <input type="text" name="name"
                            class="w-full border rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Contoh: Tas Ransel" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <input type="text" name="category"
                            class="w-full border rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Contoh: Fashion" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Produk</label>
                    <textarea name="description" rows="3"
                        class="w-full border rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Tulis deskripsi singkat produk..."></textarea>
                </div>
                <div class="grid gap-4 lg:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Beli</label>
                        <input type="number" min="0" step="1" name="buy_price"
                            class="w-full border rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Contoh: 150000" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Sewa</label>
                        <input type="number" min="0" step="1" name="rent_price"
                            class="w-full border rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Contoh: 50000" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stok</label>
                        <input type="number" min="0" step="1" name="stock"
                            class="w-full border rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Contoh: 20" required />
                    </div>
                </div>
                <div class="grid gap-4 lg:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Produk (URL)</label>
                        <input type="url" name="image"
                            class="w-full border rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Contoh: https://..." />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Toko</label>
                        <select name="store_id"
                            class="w-full border rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Pilih toko (opsional)</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}">{{ $store->nama_toko }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kurir Terkait</label>
                    <select name="couriers[]" multiple
                        class="w-full border rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-green-500" size="6">
                        @foreach($couriers as $courier)
                            <option value="{{ $courier->id }}">{{ $courier->name }} - {{ $courier->service }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-2">Tahan Ctrl/Cmd untuk memilih lebih dari satu kurir.</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" id="cancelProductModal"
                        class="bg-gray-200 text-gray-700 px-5 py-3 rounded-xl">Batal</button>
                    <button type="submit" class="bg-green-700 text-white px-5 py-3 rounded-xl">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>

    @if($products->isEmpty())
        <div class="bg-white p-6 rounded-3xl shadow text-gray-500">Tidak ada produk pending saat ini.</div>
    @else
        @foreach($products as $p)
            <div class="bg-white border border-slate-200 rounded-3xl shadow-sm p-6 mb-4 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-3 lg:max-w-3xl">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="inline-flex rounded-full bg-emerald-100 text-emerald-800 px-3 py-1 text-xs font-semibold">Validasi Produk</span>
                        <span class="text-sm text-slate-500">{{ $p->created_at ? $p->created_at->diffForHumans() : '' }}</span>
                    </div>
                    <h2 class="text-xl font-semibold text-slate-900">{{ $p->name }}</h2>
                    @if($p->category)
                        <p class="text-sm text-slate-600">Kategori: {{ $p->category }}</p>
                    @endif
                    @if($p->description)
                        <p class="text-sm text-slate-600">{{ Str::limit($p->description, 100) }}</p>
                    @endif
                    <div class="grid gap-2 sm:grid-cols-3">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Harga Beli</p>
                            <p class="text-lg font-semibold text-slate-800">Rp {{ number_format($p->buy_price, 0, ',', '.') }}</p>
                        </div>
                        @if($p->rent_price > 0)
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Harga Sewa</p>
                            <p class="text-lg font-semibold text-slate-800">Rp {{ number_format($p->rent_price, 0, ',', '.') }}</p>
                        </div>
                        @endif
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Stok</p>
                            <p class="text-lg font-semibold text-slate-800">{{ $p->stock }}</p>
                        </div>
                    </div>
                    @if($p->store)
                        <p class="text-sm text-slate-600">Toko: {{ $p->store->nama_toko }}</p>
                    @endif
                    @if($p->flag_reason)
                        <p class="rounded-2xl bg-yellow-50 border border-yellow-200 p-3 text-sm text-yellow-800">Auto flag: {{ $p->flag_reason }}</p>
                    @endif
                    <p class="text-sm text-slate-600">Kurir: {{ $p->couriers->count() ? $p->couriers->map(fn($c) => $c->name . ' ' . $c->service)->join(', ') : 'Belum terhubung ke kurir apa pun.' }}</p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="/admin/products/approve/{{ $p->id }}" class="inline-flex items-center justify-center rounded-3xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Mengizinkan</a>
                    <a href="/admin/products/reject/{{ $p->id }}" class="inline-flex items-center justify-center rounded-3xl bg-red-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-red-700">Tidak Mengizinkan</a>
                </div>
            </div>
        @endforeach
    @endif

    <script>
        const productModal = document.getElementById('productModal');
        const openProductModalButton = document.getElementById('openProductModal');
        const closeProductModalButton = document.getElementById('closeProductModal');
        const cancelProductModalButton = document.getElementById('cancelProductModal');

        function openProductModal() {
            productModal.classList.remove('hidden');
        }

        function closeProductModal() {
            productModal.classList.add('hidden');
        }

        openProductModalButton.addEventListener('click', openProductModal);
        closeProductModalButton.addEventListener('click', closeProductModal);
        cancelProductModalButton.addEventListener('click', closeProductModal);
        window.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !productModal.classList.contains('hidden')) {
                closeProductModal();
            }
        });
    </script>

@endsection
