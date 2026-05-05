@extends('layouts.app_pembeli')

@section('content')

<div class="pb-20">

    {{-- HERO --}}
    <section class="relative overflow-hidden">
        <div class="relative h-[680px]">
            <div class="absolute inset-0 overflow-hidden">
                <div id="heroCarousel" class="h-full relative">
                    @foreach([1,2,3] as $slide)
                        <div class="hero-slide absolute inset-0 {{ $slide === 1 ? 'opacity-100' : 'opacity-0' }} transition-opacity duration-700 ease-in-out" data-slide="{{ $slide }}">
                            <img src="https://picsum.photos/seed/alam{{ $slide }}/1920/1080" class="absolute inset-0 h-full w-full object-cover brightness-90">
                            <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/20 to-transparent"></div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="absolute inset-0 z-10 flex flex-col justify-center px-4 sm:px-6 lg:px-12">
                <div class="mx-auto flex max-w-7xl flex-col gap-8 lg:flex-row lg:items-center lg:justify-between">
                    <div class="max-w-2xl text-white">
                        <span class="inline-flex items-center rounded-full bg-white/10 px-4 py-2 text-xs uppercase tracking-[0.28em] font-semibold text-white shadow-sm">
                            WELCOME BACK ADVENTURER!
                        </span>
                        <h1 class="mt-6 text-5xl font-bold tracking-tight sm:text-6xl">
                            Petualangan <span class="text-green-300">Menantimu</span>
                        </h1>
                        <p class="mt-6 max-w-xl text-base text-gray-100 sm:text-lg">
                            Lengkapi persiapan campingmu dengan peralatan terbaik. Beli atau sewa, pilihan ada di tanganmu.
                        </p>

                        <div class="mt-10 flex flex-col gap-4 sm:flex-row">
                            <a href="/search" class="inline-flex items-center justify-center rounded-full bg-green-600 px-8 py-4 text-sm font-semibold text-white shadow-xl shadow-green-600/20 hover:bg-green-700">
                                Mulai Belanja
                            </a>
                            <a href="/articles" class="inline-flex items-center justify-center rounded-full border border-white/30 bg-white/10 px-8 py-4 text-sm font-semibold text-white hover:bg-white/20">
                                Lihat Promo
                            </a>
                        </div>
                    </div>

                    <div class="hidden xl:flex relative items-center justify-center">
                        <button type="button" onclick="prevSlide()" class="absolute left-0 z-20 flex h-14 w-14 items-center justify-center rounded-full border border-white/25 bg-white/10 text-2xl text-white shadow-lg shadow-black/20 hover:bg-white/20">
                            ‹
                        </button>
                        <div class="relative w-[360px] rounded-[40px] border border-white/20 bg-white/10 p-4 backdrop-blur-xl shadow-2xl shadow-black/20">
                            <img id="heroPreview" src="https://picsum.photos/seed/alam1/760/540" class="w-full rounded-[32px] object-cover" alt="Outdoor landscape">
                        </div>
                        <button type="button" onclick="nextSlide()" class="absolute right-0 z-20 flex h-14 w-14 items-center justify-center rounded-full border border-white/25 bg-white/10 text-2xl text-white shadow-lg shadow-black/20 hover:bg-white/20">
                            ›
                        </button>
                    </div>
                </div>
            </div>

            <div class="absolute left-5 top-1/2 z-20 hidden lg:flex h-14 w-14 items-center justify-center rounded-full border border-white/30 bg-white/10 text-3xl text-white shadow-xl shadow-black/25 cursor-pointer hover:bg-white/20" onclick="prevSlide()">
                ‹
            </div>
            <div class="absolute right-5 top-1/2 z-20 hidden lg:flex h-14 w-14 items-center justify-center rounded-full border border-white/30 bg-white/10 text-3xl text-white shadow-xl shadow-black/25 cursor-pointer hover:bg-white/20" onclick="nextSlide()">
                ›
            </div>

            <div class="absolute bottom-8 left-1/2 z-20 flex -translate-x-1/2 gap-3">
                @foreach([1,2,3] as $dot)
                    <button type="button" onclick="goSlide({{ $dot }})" class="hero-dot h-3 w-3 rounded-full {{ $dot === 1 ? 'bg-white' : 'bg-white/50' }} transition-all duration-300"></button>
                @endforeach
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const slides = document.querySelectorAll('.hero-slide');
            const dots = document.querySelectorAll('.hero-dot');
            const preview = document.getElementById('heroPreview');
            let current = 1;
            const total = slides.length;
            let interval = null;

            function updateSlide() {
                slides.forEach(slide => {
                    const isActive = Number(slide.dataset.slide) === current;
                    slide.classList.toggle('opacity-100', isActive);
                    slide.classList.toggle('opacity-0', !isActive);
                });
                dots.forEach((dot, index) => {
                    const active = index + 1 === current;
                    dot.classList.toggle('bg-white', active);
                    dot.classList.toggle('bg-white/50', !active);
                });
                if (preview) {
                    preview.src = `https://picsum.photos/seed/alam${current}/760/540`;
                }
            }

            window.nextSlide = function () {
                current = current === total ? 1 : current + 1;
                updateSlide();
            };

            window.prevSlide = function () {
                current = current === 1 ? total : current - 1;
                updateSlide();
            };

            window.goSlide = function (slide) {
                current = slide;
                updateSlide();
            };

            interval = setInterval(nextSlide, 5000);
            updateSlide();
        });
    </script>

    {{-- CATEGORIES --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl font-bold mb-10">Kategori Populer</h2>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                @foreach($categories as $cat)
                    <a href="/category/{{ $cat }}" class="p-6 bg-gray-50 rounded-xl text-center hover:shadow">
                        <span class="font-bold">{{ $cat }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- PRODUCTS --}}
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl font-bold mb-10">Rekomendasi Produk</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($produks as $product)
                    @include('components.product-card', [
                        'product' => $product,
                        'isWishlisted' => in_array($product->id, $wishlistProduksIds),
                    ])
                @endforeach
            </div>
        </div>
    </section>

    {{-- POPULAR PRODUCTS --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl font-bold mb-10">Produk Populer</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($popularProduks as $product)
                    @include('components.product-card', [
                        'product' => $product,
                        'isWishlisted' => in_array($product->id, $wishlistProduksIds),
                    ])
                @endforeach
            </div>
        </div>
    </section>



</div>

@endsection