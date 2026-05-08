@extends('layouts.app_pembeli')

@section('content')
<div class="max-w-7xl mx-auto py-12 px-4">
    <h1 class="text-3xl font-bold mb-6">Wishlist</h1>

    @if($wishlists->isEmpty())
        <div class="text-center py-20 bg-white rounded-3xl shadow-sm">
            <h2 class="text-xl font-semibold">Wishlist kosong</h2>
            <p class="text-gray-500 mt-3">Tambahkan produk favoritmu untuk simpan nanti.</p>
            <a href="{{ route('produk.index') }}" class="mt-6 inline-block bg-green-600 text-white px-6 py-3 rounded-full hover:bg-green-700 transition">
                Jelajahi Produk
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($wishlists as $wishlist)
                <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
                    <a href="{{ route('produk.detail', $wishlist->product->id) }}">
                        <img src="{{ $wishlist->product->image ? asset('storage/' . $wishlist->product->image) : 'https://via.placeholder.com/400x300' }}" class="w-full h-56 object-cover">
                    </a>
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-full">{{ $wishlist->product->category }}</span>
                            <span class="text-yellow-500 font-semibold">⭐ {{ $wishlist->product->rating ?? 4.5 }}</span>
                        </div>
                        <a href="{{ route('produk.detail', $wishlist->product->id) }}" class="block font-semibold text-lg text-slate-900 mb-3">{{ $wishlist->product->name }}</a>
                        <div class="text-slate-700 mb-4">
                            Rp {{ number_format($wishlist->product->buy_price) }}
                            @if(!empty($wishlist->product->rent_price) && $wishlist->product->rent_price > 0)
                                <span class="block text-sm text-emerald-600">Sewa: Rp {{ number_format($wishlist->product->rent_price) }}/hari</span>
                            @endif
                        </div>
                        <form action="{{ route('wishlist.toggle') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $wishlist->product->id }}">
                            <button class="w-full text-center bg-red-500 text-white py-3 rounded-2xl hover:bg-red-600 transition">Hapus dari Wishlist</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection