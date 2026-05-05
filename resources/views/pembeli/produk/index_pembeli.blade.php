@extends('layouts.app_pembeli')

@section('content')

{{-- ================= HERO ================= --}}
<div class="relative -mt-40">
    <div class="h-[520px] w-full relative">

        <!-- Background -->
        <img src="https://images.unsplash.com/photo-1501785888041-af3ef285b470"
             class="absolute inset-0 w-full h-full object-cover">

        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/50"></div>

        <!-- Content -->
        <div class="relative z-10 h-full flex items-center">
            <div class="max-w-7xl mx-auto px-6 text-white">

                <span class="bg-white/20 px-4 py-1 rounded-full text-sm mb-4 inline-block backdrop-blur">
                    WELCOME BACK ADVENTURER!
                </span>

                <h1 class="text-5xl md:text-6xl font-bold">
                    Petualangan <span class="text-green-400">Menantimu</span>
                </h1>

                <p class="mt-4 max-w-xl text-gray-200">
                    Lengkapi persiapan campingmu dengan peralatan terbaik.
                </p>

                <div class="mt-6 flex gap-4">
                    <button class="bg-green-600 px-6 py-3 rounded-full hover:bg-green-700">
                        Mulai Belanja
                    </button>
                    <button class="border border-white/50 px-6 py-3 rounded-full hover:bg-white/10">
                        Lihat Promo
                    </button>
                </div>

            </div>
        </div>

    </div>
</div>

{{-- ================= CONTENT ================= --}}
<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- KATEGORI --}}
    <div class="mb-8">
        <h2 class="text-xl font-bold mb-4">Kategori Populer</h2>

        <div class="flex flex-wrap gap-3">
            @foreach(['Tenda','Sleeping Bag','Alat Masak','Tas Gunung','Aksesoris'] as $kat)
                <span class="px-4 py-2 bg-gray-100 rounded-full text-sm hover:bg-green-100 cursor-pointer">
                    {{ $kat }}
                </span>
            @endforeach
        </div>
    </div>

    {{-- PRODUK --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($produks as $product)
            @include('components.product-card', [
                'product' => $product,
                'isWishlisted' => in_array($product->id, $wishlistProductIds ?? []),
            ])
        @endforeach
    </div>

    <div class="mt-8">
        {{ $produks->links() }}
    </div>

</div>
@endsection