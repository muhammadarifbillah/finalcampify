@extends('layouts.app_pembeli')

@section('content')
<div class="max-w-5xl mx-auto py-12 px-4">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-12">
        <div>
            <h1 class="text-4xl font-bold text-forest-950">{{ $article->title }}</h1>
            <p class="text-sm text-gray-500 mt-2">{{ $article->author }} • {{ $article->date }}</p>
        </div>
        <a href="{{ (request()->query('from') === 'admin' || (auth()->check() && auth()->user()->role === 'admin')) ? '/admin/articles/show/' . $article->id : route('articles.index') }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-forest-100 bg-white px-5 py-2.5 text-sm font-semibold text-forest-800 hover:bg-forest-50 whitespace-nowrap shrink-0">
            ← Kembali ke Artikel
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-forest-100">
        @if($article->image)
            <img src="{{ Str::startsWith($article->image, 'http') ? $article->image : asset($article->image) }}" alt="{{ $article->title }}" class="w-full object-cover max-h-[420px]">
        @endif

        <div class="p-10 prose max-w-none text-earth-700">
            {!! nl2br(e($article->content)) !!}
        </div>
    </div>
</div>
@endsection