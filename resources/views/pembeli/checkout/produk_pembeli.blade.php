@extends('layouts.app_pembeli')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-slate-50 to-white py-8 lg:py-12">
    <div class="max-w-4xl mx-auto px-4">

        {{-- HEADER --}}
        <div class="mb-8">
            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Checkout</h1>
            <p class="text-gray-600">Pilih jenis transaksi untuk produk yang ingin Anda dapatkan</p>
        </div>

        {{-- PRODUCT CARD --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6 md:p-8">
                
                {{-- PRODUCT IMAGE --}}
                <div class="md:col-span-1">
                    <div class="bg-slate-100 rounded-xl overflow-hidden aspect-square">
                        <img 
                            src="{{ $produk->image ? asset('storage/' . $produk->image) : 'https://via.placeholder.com/400x400' }}"
                            alt="{{ $produk->name }}"
                            class="w-full h-full object-cover"
                        >
                    </div>
                </div>

                {{-- PRODUCT INFO --}}
                <div class="md:col-span-2">
                    <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-3">{{ $produk->name }}</h2>
                    
                    <div class="flex items-center gap-4 mb-4 pb-4 border-b border-slate-200">
                        <div class="flex text-yellow-400 text-lg">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $produk->rating)
                                    <span>★</span>
                                @else
                                    <span>☆</span>
                                @endif
                            @endfor
                        </div>
                        <span class="text-sm text-gray-600">({{ $produk->reviews_count }} ulasan)</span>
                    </div>

                    <p class="text-gray-700 mb-6 leading-relaxed">{{ $produk->description }}</p>

                    {{-- STOCK STATUS --}}
                    <div class="flex items-center gap-3 mb-6">
                        <span class="px-4 py-2 bg-green-100 text-green-800 text-sm font-bold rounded-full">
                            {{ $produk->stock > 0 ? 'Tersedia' : 'Habis' }}
                        </span>
                        <span class="text-gray-600">Stok: <strong>{{ $produk->stock }} unit</strong></span>
                    </div>

                    {{-- PRICING OPTIONS --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-green-50 border-2 border-green-200 rounded-lg p-4">
                            <p class="text-sm text-gray-600 mb-1">Harga Beli</p>
                            <p class="text-2xl font-bold text-green-600">Rp {{ number_format($produk->buy_price) }}</p>
                        </div>
                        <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4">
                            <p class="text-sm text-gray-600 mb-1">Harga Sewa</p>
                            <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($produk->rent_price) }}/hari</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- CHECKOUT FORMS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            {{-- BELI FORM --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 border-t-4 border-green-500">
                <div class="flex items-center gap-2 mb-6">
                    <span class="text-3xl">🛒</span>
                    <h3 class="text-2xl font-bold text-gray-900">Beli Sekarang</h3>
                </div>

                <form action="{{ route('cart.add') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="produk_id" value="{{ $produk->id }}">
                    <input type="hidden" name="type" value="buy">
                    <input type="hidden" name="redirect" value="checkout">

                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Jumlah Pembelian</label>
                        <input 
                            type="number" 
                            name="quantity" 
                            value="1" 
                            min="1" 
                            max="{{ $produk->stock }}"
                            class="w-full border-2 border-slate-200 rounded-lg px-4 py-3 focus:border-green-500 focus:outline-none font-semibold text-lg"
                            id="buy-quantity"
                            onchange="calculateBuyTotal()"
                            oninput="calculateBuyTotal()"
                        >
                        <p class="text-xs text-gray-600 mt-2">Maksimal: {{ $produk->stock }} unit</p>
                    </div>

                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                        <p class="text-sm text-gray-600 mb-1">Total Harga</p>
                        <p class="text-3xl font-bold text-green-600" id="buy-total">Rp {{ number_format($produk->buy_price) }}</p>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg transition shadow-md hover:shadow-lg"
                    >
                        Lanjut ke Pembayaran
                    </button>
                </form>
            </div>

            {{-- SEWA FORM --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 border-t-4 border-blue-500">
                <div class="flex items-center gap-2 mb-6">
                    <span class="text-3xl">📅</span>
                    <h3 class="text-2xl font-bold text-gray-900">Sewa Sekarang</h3>
                </div>

                <form action="{{ route('cart.add') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="produk_id" value="{{ $produk->id }}">
                    <input type="hidden" name="type" value="rent">
                    <input type="hidden" name="redirect" value="checkout">

                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Tanggal Mulai</label>
                        <input 
                            type="date" 
                            name="start_date" 
                            min="{{ date('Y-m-d') }}"
                            class="w-full border-2 border-slate-200 rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none"
                            id="rent-start-date"
                            onchange="calculateRentTotal()"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Durasi Sewa (hari)</label>
                        <input 
                            type="number" 
                            name="duration" 
                            value="1" 
                            min="1" 
                            max="90"
                            class="w-full border-2 border-slate-200 rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none font-semibold text-lg"
                            id="rent-duration"
                            onchange="calculateRentTotal()"
                            oninput="calculateRentTotal()"
                        >
                        <p class="text-xs text-gray-600 mt-2">Maksimal: 90 hari</p>
                    </div>

                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <p class="text-sm text-gray-600 mb-1">Total Harga (Per Hari × Durasi)</p>
                        <p class="text-3xl font-bold text-blue-600" id="rent-total">Rp {{ number_format($produk->rent_price) }}</p>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition shadow-md hover:shadow-lg"
                    >
                        Lanjut ke Pembayaran
                    </button>
                </form>
            </div>

        </div>

        {{-- BACK BUTTON --}}
        <div class="mt-8 text-center">
            <a href="{{ route('produk.detail', $produk->id) }}" class="text-blue-600 hover:text-blue-700 font-semibold">
                ← Kembali ke Detail Produk
            </a>
        </div>

    </div>
</div>

<script>
function calculateBuyTotal() {
    const quantity = parseInt(document.getElementById('buy-quantity').value) || 1;
    const price = {{ $produk->buy_price }};
    const total = quantity * price;
    document.getElementById('buy-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function calculateRentTotal() {
    const duration = parseInt(document.getElementById('rent-duration').value) || 1;
    const price = {{ $produk->rent_price }};
    const total = duration * price;
    document.getElementById('rent-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
}
</script>
@endsection
