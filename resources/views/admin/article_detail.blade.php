@extends('layouts.admin')

@section('content')

    <div class="max-w-6xl mx-auto">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black text-slate-800 flex items-center gap-3">
                    <i data-lucide="file-text" class="w-7 h-7 text-teal-600"></i>
                    Detail Artikel
                </h1>
                <p class="text-sm text-slate-500 mt-1 font-medium">Pratinjau lengkap artikel dengan metadata dan aksi admin.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="/admin/articles"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Kembali ke Daftar
                </a>
                @if($article->status === 'draft')
                    <a href="/admin/articles/publish/{{ $article->id }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 transition">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        Publish Sekarang
                    </a>
                @else
                    <a href="/admin/articles/unpublish/{{ $article->id }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-600 transition">
                        <i data-lucide="archive" class="w-4 h-4"></i>
                        Simpan ke Draft
                    </a>
                @endif
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[2fr_1fr]">

            {{-- KONTEN UTAMA --}}
            <div class="space-y-6">
                {{-- GAMBAR HERO --}}
                @if($article->image)
                    <div class="rounded-2xl overflow-hidden border border-slate-200 shadow-sm">
                        <img src="{{ Str::startsWith($article->image, 'http') ? $article->image : asset($article->image) }}"
                             alt="{{ $article->title }}"
                             class="w-full h-80 object-cover">
                    </div>
                @endif

                {{-- ARTIKEL BODY --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200">
                    <div class="flex flex-wrap items-center gap-3 mb-5">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-teal-50 border border-teal-200 px-3 py-1 text-xs font-bold text-teal-700 uppercase tracking-wider">
                            <i data-lucide="tag" class="w-3 h-3"></i>
                            {{ $article->kategori_slug }}
                        </span>
                        @if($article->status === 'publish')
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200 px-3 py-1 text-xs font-bold text-emerald-700">
                                <i data-lucide="check-circle" class="w-3 h-3"></i> Published
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 border border-amber-200 px-3 py-1 text-xs font-bold text-amber-700">
                                <i data-lucide="edit-3" class="w-3 h-3"></i> Draft
                            </span>
                        @endif
                    </div>

                    <h2 class="text-3xl font-black text-slate-900 leading-tight mb-4">{{ $article->title }}</h2>

                    <div class="flex flex-wrap gap-4 text-xs text-slate-500 font-medium mb-6 pb-6 border-b border-slate-100">
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                            {{ $article->waktu_posting->format('d M Y, H:i') }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            {{ number_format($article->views) }} views
                        </span>
                    </div>

                    <div class="prose prose-slate max-w-none text-slate-700 leading-relaxed">
                        {!! nl2br(e($article->content)) !!}
                    </div>
                </div>

                {{-- THUMBNAIL PREVIEW --}}
                @if($article->thumbnail)
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                        <h3 class="text-sm font-bold text-slate-700 mb-3 flex items-center gap-2">
                            <i data-lucide="image" class="w-4 h-4 text-slate-400"></i>
                            Preview Thumbnail
                        </h3>
                        <div class="rounded-xl overflow-hidden border border-slate-200 inline-block">
                            <img src="{{ Str::startsWith($article->thumbnail, 'http') ? $article->thumbnail : asset($article->thumbnail) }}"
                                 alt="Thumbnail {{ $article->title }}"
                                 class="h-40 object-cover">
                        </div>
                    </div>
                @endif
            </div>

            {{-- SIDEBAR --}}
            <aside class="space-y-6">
                {{-- STATISTIK --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                            <i data-lucide="bar-chart-3" class="w-4 h-4 text-teal-600"></i>
                            Informasi Artikel
                        </h3>
                    </div>
                    <div class="divide-y divide-slate-100">
                        <div class="px-6 py-3.5 flex items-center justify-between">
                            <span class="text-xs text-slate-500 font-medium">ID</span>
                            <span class="text-xs font-bold text-slate-800">#{{ $article->id }}</span>
                        </div>
                        <div class="px-6 py-3.5 flex items-center justify-between">
                            <span class="text-xs text-slate-500 font-medium">Kategori</span>
                            <span class="text-xs font-bold text-slate-800 capitalize">{{ str_replace('-', ' ', $article->kategori_slug) }}</span>
                        </div>
                        <div class="px-6 py-3.5 flex items-center justify-between">
                            <span class="text-xs text-slate-500 font-medium">Status</span>
                            @if($article->status === 'publish')
                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-700">Publish</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-700">Draft</span>
                            @endif
                        </div>
                        <div class="px-6 py-3.5 flex items-center justify-between">
                            <span class="text-xs text-slate-500 font-medium">Total Views</span>
                            <span class="text-xs font-bold text-slate-800">{{ number_format($article->views) }}</span>
                        </div>
                        <div class="px-6 py-3.5 flex items-center justify-between">
                            <span class="text-xs text-slate-500 font-medium">Tanggal Posting</span>
                            <span class="text-xs font-bold text-slate-800">{{ $article->waktu_posting->format('d M Y') }}</span>
                        </div>
                        <div class="px-6 py-3.5 flex items-center justify-between">
                            <span class="text-xs text-slate-500 font-medium">Jam Posting</span>
                            <span class="text-xs font-bold text-slate-800">{{ $article->waktu_posting->format('H:i') }} WIB</span>
                        </div>
                        <div class="px-6 py-3.5 flex items-center justify-between">
                            <span class="text-xs text-slate-500 font-medium">Dibuat</span>
                            <span class="text-xs font-bold text-slate-800">{{ $article->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                {{-- AKSI CEPAT --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                            <i data-lucide="zap" class="w-4 h-4 text-teal-600"></i>
                            Aksi Cepat
                        </h3>
                    </div>
                    <div class="p-4 space-y-2.5">
                        <a href="/article/{{ $article->id }}" target="_blank"
                            class="flex items-center justify-center gap-2 w-full rounded-xl bg-teal-600 px-4 py-3 text-sm font-semibold text-white hover:bg-teal-700 transition">
                            <i data-lucide="external-link" class="w-4 h-4"></i>
                            Lihat Tampilan Publik
                        </a>
                        <a href="/admin/articles"
                            class="flex items-center justify-center gap-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                            Edit dari Daftar
                        </a>
                        <a href="/admin/articles/delete/{{ $article->id }}"
                            onclick="return confirm('Yakin hapus artikel ini? Aksi ini tidak bisa dibatalkan.')"
                            class="flex items-center justify-center gap-2 w-full rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-100 transition">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                            Hapus Artikel
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </div>

@endsection