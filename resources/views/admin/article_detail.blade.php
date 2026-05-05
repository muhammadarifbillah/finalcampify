@extends('layouts.admin')

@section('content')

    <div class="max-w-6xl mx-auto">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Detail Artikel</h1>
                <p class="text-gray-500 mt-1">Tampilan lengkap artikel dengan semua metadata dan aksi admin.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="/admin/articles"
                    class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Kembali ke daftar
                </a>
                @if($article->status === 'draft')
                    <a href="/admin/articles/publish/{{ $article->id }}"
                        class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                        Publish
                    </a>
                @else
                    <a href="/admin/articles/unpublish/{{ $article->id }}"
                        class="inline-flex items-center justify-center rounded-2xl bg-yellow-500 px-4 py-2 text-sm font-semibold text-white hover:bg-yellow-600">
                        Simpan ke Draft
                    </a>
                @endif
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[2fr_1fr]">

            <div class="space-y-6 bg-white rounded-3xl p-6 shadow-sm border border-gray-200">
                @if($article->thumbnail)
                    <img src="{{ $article->thumbnail }}" class="w-full h-72 object-cover rounded-3xl shadow-sm">
                @endif

                <div>
                    <span
                        class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-800">
                        {{ $article->kategori_slug }}
                    </span>
                </div>

                <h2 class="text-4xl font-bold text-gray-900">{{ $article->title }}</h2>

                <div class="flex flex-wrap gap-3 mt-4 text-sm text-gray-500">
                    <span>Status: <strong class="text-gray-800">{{ ucfirst($article->status) }}</strong></span>
                    <span>Tanggal posting: <strong
                            class="text-gray-800">{{ $article->waktu_posting->format('d M Y H:i') }}</strong></span>
                    <span>Views: <strong class="text-gray-800">{{ $article->views }}</strong></span>
                </div>

                <div class="prose prose-slate max-w-none mt-6 text-gray-700">
                    {!! nl2br(e($article->content)) !!}
                </div>

                @if($article->image)
                    <div class="rounded-3xl overflow-hidden border border-gray-200 shadow-sm">
                        <img src="{{ $article->image }}" class="w-full h-72 object-cover" alt="Gambar artikel tambahan">
                    </div>
                @endif
            </div>

            <aside class="space-y-6">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Statistik Artikel</h3>
                    <div class="space-y-3 text-sm text-gray-600">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <span>Judul</span>
                            <span class="font-semibold text-gray-900">{{ $article->title }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3 pt-3">
                            <span>Kategori</span>
                            <span class="font-semibold text-gray-900">{{ $article->kategori_slug }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3 pt-3">
                            <span>Status</span>
                            <span class="font-semibold text-gray-900">{{ ucfirst($article->status) }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3 pt-3">
                            <span>Views</span>
                            <span class="font-semibold text-gray-900">{{ $article->views }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-3">
                            <span>Waktu posting</span>
                            <span
                                class="font-semibold text-gray-900">{{ $article->waktu_posting->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Aksi Cepat</h3>
                    <div class="space-y-3 text-sm">
                        <a href="/admin/articles"
                            class="block w-full rounded-2xl bg-blue-600 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-blue-700">
                            Edit artikel dari daftar
                        </a>
                        <a href="/admin/articles/delete/{{ $article->id }}"
                            onclick="return confirm('Yakin hapus artikel ini?')"
                            class="block w-full rounded-2xl bg-red-600 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-red-700">
                            Hapus artikel
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </div>

@endsection