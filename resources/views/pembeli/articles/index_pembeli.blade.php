@extends('layouts.app_pembeli')

@section('content')
<div class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- HEADER -->
    <div class="text-center mb-16">
        <span class="text-[10px] font-bold text-forest-600 uppercase tracking-widest block mb-4">
            Campify Journal
        </span>

        <h1 class="text-4xl md:text-5xl font-bold text-forest-950 uppercase mb-6">
            Edukasi & Tips Outdoor
        </h1>

        <p class="text-sm text-earth-500 max-w-2xl mx-auto uppercase">
            Persiapkan dirimu dengan pengetahuan sebelum bertualang.
        </p>
    </div>

    <form action="{{ route('articles.index') }}" method="GET" class="mb-12 max-w-xl mx-auto relative">
        <input
            type="text"
            name="q"
            value="{{ old('q', $query ?? '') }}"
            placeholder="Cari artikel..."
            class="w-full px-12 py-4 bg-white border border-forest-100 rounded-2xl shadow-sm uppercase text-[10px] font-bold"
        >
        <span class="absolute left-4 top-4 text-forest-300">🔍</span>
    </form>

    <!-- LIST ARTIKEL -->
    @if($articles->isEmpty())
        <div class="text-center py-20 rounded-3xl border border-forest-100 bg-white shadow-sm">
            <h3 class="text-xl font-bold text-forest-900 mb-3">Tidak ada artikel ditemukan</h3>
            <p class="text-sm text-earth-500">Coba kata kunci lain atau lihat artikel terbaru di bawah.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            @foreach($articles as $article)
                <a href="{{ route('articles.show', $article->id) }}"
                   class="group bg-white rounded-3xl border border-forest-100 overflow-hidden shadow-sm hover:shadow-lg transition">

                    <div class="aspect-[16/10] overflow-hidden">
                        <img src="{{ $article->image }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    </div>

                    <div class="p-6">
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <span class="text-[10px] font-bold text-forest-600 uppercase tracking-[0.25em]">
                                {{ $article->category }}
                            </span>
                            <span class="text-[10px] text-gray-400 uppercase tracking-[0.2em]">
                                {{ $article->date }}
                            </span>
                        </div>

                        <h3 class="font-bold text-2xl text-earth-950 mb-3 group-hover:text-forest-600 transition">
                            {{ $article->title }}
                        </h3>

                        <p class="text-sm text-earth-500 leading-6 line-clamp-3">
                            {{ $article->excerpt }}
                        </p>

                        <div class="mt-6 text-xs font-semibold text-forest-700 uppercase tracking-[0.2em]">
                            Baca Selengkapnya →
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    <!-- QUICK TIPS -->
    <div class="mt-24 grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="p-8 bg-forest-900 rounded-3xl text-white">
            <h4 class="text-sm font-bold mb-4 uppercase">Packing Efisien</h4>
            <p class="text-xs text-forest-300">Cara menyusun tas agar seimbang.</p>
        </div>

        <div class="p-8 bg-forest-900 rounded-3xl text-white">
            <h4 class="text-sm font-bold mb-4 uppercase">Memilih Lokasi</h4>
            <p class="text-xs text-forest-300">Tips spot tenda yang aman.</p>
        </div>

        <div class="p-8 bg-forest-900 rounded-3xl text-white">
            <h4 class="text-sm font-bold mb-4 uppercase">Etika Outdoor</h4>
            <p class="text-xs text-forest-300">Prinsip Leave No Trace.</p>
        </div>
    </div>

</div>
@endsection