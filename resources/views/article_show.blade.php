<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $article->title }} - Campify</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 text-slate-900">
    <div class="min-h-screen py-10 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-5xl bg-white rounded-3xl shadow-lg overflow-hidden">
            @if($article->thumbnail)
                <img src="{{ $article->thumbnail }}" class="w-full h-96 object-cover" alt="Thumbnail artikel">
            @endif
            <div class="p-8">
                <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500 mb-4">
                    <span class="rounded-full bg-slate-100 px-3 py-1">{{ $article->kategori_slug }}</span>
                    <span
                        class="rounded-full bg-emerald-100 px-3 py-1 text-emerald-700">{{ ucfirst($article->status) }}</span>
                    <span>{{ $article->waktu_posting->format('d M Y H:i') }}</span>
                    <span>👁 {{ $article->views }} views</span>
                </div>
                <h1 class="text-4xl font-bold text-slate-900 mb-6">{{ $article->title }}</h1>
                <div class="prose prose-slate max-w-none text-slate-700">
                    {!! nl2br(e($article->content)) !!}
                </div>
                @if($article->image)
                    <div class="mt-8 overflow-hidden rounded-3xl border border-slate-200 shadow-sm">
                        <img src="{{ $article->image }}" class="w-full h-80 object-cover" alt="Gambar tambahan artikel">
                    </div>
                @endif
                <div class="mt-10 flex flex-wrap gap-3 text-sm text-slate-500">
                    <span>Artikel disajikan oleh Campify</span>
                    <span>{{ $article->views }} kali dibaca</span>
                </div>
            </div>
        </div>
    </div>
</body>

</html>