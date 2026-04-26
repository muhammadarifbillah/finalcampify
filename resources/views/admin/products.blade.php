@extends('layouts.admin')

@section('content')

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Validasi Produk</h1>
            <p class="text-gray-600">Kelola produk baru dan tautkan ke kurir yang tersedia.</p>
        </div>
        <button id="openProductModal"
            class="bg-green-700 text-white px-5 py-3 rounded-xl font-semibold hover:bg-green-800">Tambah Produk</button>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 border border-green-200 p-4 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div id="productModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
        <div class="w-full max-w-2xl rounded-3xl bg-white p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold">Tambah Produk</h2>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Produk</label>
                        <input type="number" min="0" step="1" name="price"
                            class="w-full border rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Contoh: 150000" required />
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
        <div class="bg-white p-6 rounded-xl shadow text-gray-500">Tidak ada produk pending saat ini.</div>
    @else
        @foreach($products as $p)
            <div class="bg-white p-4 rounded-xl shadow mb-3 flex flex-col gap-3 lg:flex-row lg:justify-between lg:items-center">
                <div>
                    <h2 class="font-bold text-lg">{{ $p->name }}</h2>
                    <p class="text-gray-500">Status: {{ ucfirst($p->status) }}</p>
                    <p class="text-gray-500">Harga: Rp {{ number_format($p->price, 0, ',', '.') }}</p>
                    @if($p->couriers->count())
                        <p class="text-gray-600 text-sm">Kurir: {{ $p->couriers->pluck('name')->join(', ') }}</p>
                    @else
                        <p class="text-gray-600 text-sm">Belum terhubung ke kurir apa pun.</p>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="/admin/products/approve/{{ $p->id }}" class="bg-green-500 text-white px-4 py-2 rounded-xl">Approve</a>
                    <a href="/admin/products/reject/{{ $p->id }}" class="bg-red-500 text-white px-4 py-2 rounded-xl">Reject</a>
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