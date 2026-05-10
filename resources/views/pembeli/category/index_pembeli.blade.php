@extends('layouts.app_pembeli')

@section('content')
<div class="py-12 max-w-7xl mx-auto px-4">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-12 gap-6">
        <div>
            <span class="text-[10px] font-bold text-green-600 uppercase tracking-widest block mb-2">
                Kategori
            </span>
            <h1 class="text-4xl font-bold uppercase">
                {{ $category }}
            </h1>
            <p class="text-sm text-gray-500 mt-2">
                Menampilkan {{ $products->count() }} produk untuk {{ $category }}
            </p>
        </div>

        <!-- Dummy Filter (optional) -->
        <div class="flex gap-4">
            <button class="px-4 py-2 border rounded-xl text-xs font-bold">
                Filter
            </button>
            <button class="px-4 py-2 border rounded-xl text-xs font-bold">
                Urutkan
            </button>
        </div>
    </div>

    <!-- CONTENT -->
    @if($products->isEmpty())
        <div class="py-20 text-center">
            <p class="text-gray-400">Belum ada produk di kategori ini.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

            @foreach($products as $product)
                <div class="bg-white rounded-2xl shadow p-4">

                    <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-48 object-cover rounded-xl mb-4">

                    <span class="text-xs text-green-600 font-bold uppercase">
                        {{ $product->category }}
                    </span>

                    <h3 class="font-bold mt-2">
                        {{ $product->name }}
                    </h3>

                    <div class="mt-3 text-sm">
                        <div class="flex justify-between">
                            <span>Beli</span>
                            <span class="font-bold">
                                Rp {{ number_format($product->buy_price,0,',','.') }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span>Sewa</span>
                            <span class="font-bold text-green-600">
                                Rp {{ number_format($product->rent_price,0,',','.') }}/hari
                            </span>
                        </div>
                    </div>

                    <a href="/product/{{ $product->id }}" 
                       class="block mt-4 text-center bg-gray-100 py-2 rounded-xl text-sm font-bold hover:bg-green-600 hover:text-white">
                        Detail Produk
                    </a>
                </div>
            @endforeach

        </div>
    @endif

</div>
@endsection