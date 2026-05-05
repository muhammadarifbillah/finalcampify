@extends('layouts.app_pembeli')

@section('content')
<div class="py-12 max-w-7xl mx-auto px-4">

    <h1 class="text-3xl font-bold mb-10">Keranjang Belanja</h1>

    @if($cart->isEmpty())
        <div class="text-center py-20">
            <h2 class="text-xl font-bold">Keranjang kosong</h2>
            <a href="/" class="btn-primary mt-4 inline-block">Belanja Sekarang</a>
        </div>
    @else

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

        <!-- ITEMS -->
        <div class="lg:col-span-2 space-y-6">
            @foreach($cart as $item)
                @php
                    $price = $item->type === 'buy'
                        ? $item->product->buy_price
                        : $item->product->rent_price * $item->duration;
                    $total = $price * $item->qty;
                @endphp

                <div class="bg-white p-6 rounded-2xl flex gap-6 shadow">

                    <img src="{{ $item->product->image }}" class="w-24 h-24 object-cover rounded-xl">

                    <div class="flex-1">
                        <h3 class="font-bold">{{ $item->product->name }}</h3>
                        <p class="text-sm text-gray-500">
                            {{ $item->type === 'buy' ? 'Beli' : 'Sewa' }}
                            @if($item->type === 'rent')
                                ({{ $item->duration }} hari mulai {{ $item->start_date->format('d/m/Y') }})
                            @endif
                        </p>

                        <p class="font-bold text-green-700 mt-2">
                            Rp {{ number_format($total) }}
                        </p>

                        <!-- QUANTITY/DURATION -->
                        <div class="flex items-center mt-4 gap-2">
                            @if($item->type === 'buy')
                                <form method="POST" action="{{ route('cart.update', $item->id) }}" class="flex items-center gap-2">
                                    @csrf
                                    <input type="number" name="quantity" value="{{ $item->qty }}" min="1" 
                                           class="border rounded px-2 py-1 w-16" onchange="this.form.submit()">
                                </form>
                            @else
                                <form method="POST" action="{{ route('cart.update', $item->id) }}" class="flex items-center gap-2">
                                    @csrf
                                    <input type="number" name="duration" value="{{ $item->duration }}" min="1" 
                                           class="border rounded px-2 py-1 w-16" onchange="this.form.submit()">
                                    <span class="text-sm">hari</span>
                                </form>
                            @endif
                        </div>

                        <!-- REMOVE -->
                        <form method="POST" action="{{ route('cart.remove', $item->id) }}" class="mt-3">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 text-sm hover:text-red-700">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- SUMMARY -->
        <div class="bg-gray-900 text-white p-6 rounded-2xl h-fit">
            <h2 class="text-lg font-bold mb-6">Ringkasan</h2>

            @php
                $subtotal = 0;
                foreach($cart as $item) {
                    $price = $item->type === 'buy' 
                        ? $item->product->buy_price 
                        : $item->product->rent_price * $item->duration;
                    $subtotal += $price * $item->qty;
                }
            @endphp

            <div class="flex justify-between mb-2">
                <span>Subtotal</span>
                <span>Rp {{ number_format($subtotal) }}</span>
            </div>

            <div class="flex justify-between font-bold text-xl mt-4">
                <span>Total</span>
                <span>Rp {{ number_format($subtotal) }}</span>
            </div>

            <a href="{{ route('checkout.index') }}" class="block mt-6 bg-green-500 text-center py-3 rounded-xl hover:bg-green-600">
                Checkout
            </a>
        </div>

    </div>
    @endif
</div>
@endsection