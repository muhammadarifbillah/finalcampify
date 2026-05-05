@extends('layouts.admin')

@section('content')

    <div class="max-w-7xl mx-auto">

        <!-- HEADER -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Manajemen Artikel</h1>
                <p class="text-gray-500 mt-1">Ringkas, mudah scan, dan siap untuk alur draft → publish.</p>
            </div>
            <button id="openArticleModal"
                class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-2xl shadow font-semibold transition">
                + Tambah Artikel
            </button>
        </div>

        <!-- ALERT -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-4 mb-6 md:grid-cols-3">
            <div class="bg-white rounded-3xl p-5 shadow-sm">
                <p class="text-sm text-gray-500">Total Artikel</p>
                <p class="text-3xl font-semibold text-gray-800">{{ $articles->count() }}</p>
            </div>
            <div class="bg-white rounded-3xl p-5 shadow-sm">
                <p class="text-sm text-gray-500">Draft</p>
                <p class="text-3xl font-semibold text-yellow-600">{{ $draftCount }}</p>
            </div>
            <div class="bg-white rounded-3xl p-5 shadow-sm">
                <p class="text-sm text-gray-500">Publish</p>
                <p class="text-3xl font-semibold text-green-600">{{ $publishCount }}</p>
            </div>
        </div>

        <div class="overflow-x-auto bg-white rounded-3xl shadow-sm border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Judul
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Kategori
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Tanggal
                            posting</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Views
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Action
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($articles as $article)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-5 align-top">

                                <!-- 🔥 TAMBAHAN THUMBNAIL -->
                                <a href="/admin/articles/show/{{ $article->id }}">
                                    <img src="{{ $article->thumbnail ?? 'https://via.placeholder.com/80' }}"
                                        onerror="this.src='https://via.placeholder.com/80'"
                                        class="w-28 h-24 md:w-32 md:h-24 object-cover rounded-2xl border shadow-sm hover:scale-105 transition duration-200">
                                </a>

                                <a href="/admin/articles/show/{{ $article->id }}"
                                    class="block text-gray-800 font-semibold hover:text-emerald-700">
                                    {{ $article->title }}
                                </a>
                                <p class="text-sm text-gray-500 mt-1 line-clamp-2">
                                    {{ \Illuminate\Support\Str::limit($article->content, 90) }}
                                </p>
                            </td>
                            <td class="px-6 py-5 align-top">
                                <span
                                    class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                                    {{ $article->kategori_slug }}
                                </span>
                            </td>
                            <td class="px-6 py-5 align-top">
                                <span
                                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $article->status === 'publish' ? 'bg-emerald-100 text-emerald-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ ucfirst($article->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-5 align-top text-sm text-gray-600">
                                {{ $article->waktu_posting->format('d M Y') }}
                            </td>
                            <td class="px-6 py-5 align-top text-sm text-gray-600">
                                {{ $article->views }} kali
                            </td>
                            <td class="px-6 py-5 align-top text-right space-y-2">
                                <a href="/admin/articles/show/{{ $article->id }}"
                                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                    Detail
                                </a>
                                <button onclick='openEditArticle(@json($article))'
                                    class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700 w-full">
                                    Edit
                                </button>
                                @if($article->status === 'draft')
                                    <a href="/admin/articles/publish/{{ $article->id }}"
                                        class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700 w-full">
                                        Publish
                                    </a>
                                @else
                                    <a href="/admin/articles/unpublish/{{ $article->id }}"
                                        class="inline-flex items-center justify-center rounded-xl bg-yellow-500 px-3 py-2 text-xs font-semibold text-white hover:bg-yellow-600 w-full">
                                        Unpublish
                                    </a>
                                @endif
                                <a href="/admin/articles/delete/{{ $article->id }}"
                                    onclick="return confirm('Yakin hapus artikel ini?')"
                                    class="inline-flex items-center justify-center rounded-xl bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700 w-full">
                                    Hapus
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                Belum ada artikel. Tambahkan artikel baru menggunakan tombol di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <!-- MODAL -->
    <div id="articleModal" class="fixed inset-0 hidden items-center justify-center bg-black/40 z-50">
        <div class="bg-white w-full max-w-2xl rounded-3xl p-6 shadow-xl">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 id="articleModalTitle" class="text-xl font-semibold">Tambah Artikel</h2>
                    <p class="text-sm text-gray-500">Form tambah / edit artikel dengan field lengkap.</p>
                </div>
                <button id="closeArticleModal" class="text-gray-500 hover:text-gray-800">✕</button>
            </div>

            <form id="articleForm" method="POST" action="/admin/articles/store" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Judul</label>
                    <input id="articleTitle" type="text" name="title" class="w-full border border-gray-300 p-3 rounded-xl"
                        placeholder="Judul artikel" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Konten</label>
                    <textarea id="articleContent" name="content" class="w-full border border-gray-300 p-3 rounded-xl"
                        rows="6" placeholder="Konten lengkap artikel" required></textarea>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                        <select id="articleCategory" name="kategori_slug"
                            class="w-full border border-gray-300 p-3 rounded-xl" required>
                            <option value="">Pilih kategori</option>
                            <option value="outdoor">Outdoor</option>
                            <option value="tips">Tips</option>
                            <option value="review">Review</option>
                            <option value="panduan">Panduan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                        <select id="articleStatus" name="status" class="w-full border border-gray-300 p-3 rounded-xl"
                            required>
                            <option value="draft">Draft</option>
                            <option value="publish">Publish</option>
                        </select>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Waktu posting</label>
                        <input id="articleTime" type="datetime-local" name="waktu_posting"
                            class="w-full border border-gray-300 p-3 rounded-xl" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Thumbnail</label>
                        <input id="articleThumbnail" type="text" name="thumbnail"
                            class="w-full border border-gray-300 p-3 rounded-xl" placeholder="URL Thumbnail" required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Gambar tambahan (opsional)</label>
                    <input id="articleImage" type="text" name="image" class="w-full border border-gray-300 p-3 rounded-xl"
                        placeholder="URL gambar tambahan">
                </div>

                <div id="thumbnailPreview" class="hidden">
                    <p class="text-sm text-gray-500 mb-2">Preview Thumbnail</p>
                    <img id="previewThumbnail" class="w-full h-40 object-cover rounded-xl">
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" id="cancelArticleModal"
                        class="bg-gray-200 px-5 py-2 rounded-xl text-sm font-semibold">Batal</button>
                    <button class="bg-green-600 text-white px-5 py-2 rounded-xl text-sm font-semibold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('articleModal');
        const preview = document.getElementById('previewThumbnail');
        const previewWrapper = document.getElementById('thumbnailPreview');

        const title = document.getElementById('articleTitle');
        const content = document.getElementById('articleContent');
        const image = document.getElementById('articleImage');
        const thumb = document.getElementById('articleThumbnail');
        const category = document.getElementById('articleCategory');
        const status = document.getElementById('articleStatus');
        const time = document.getElementById('articleTime');
        const form = document.getElementById('articleForm');

        function openArticleModal(mode = 'add', data = null) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            if (mode === 'add') {
                form.action = '/admin/articles/store';
                title.value = '';
                content.value = '';
                image.value = '';
                thumb.value = '';
                category.value = '';
                status.value = 'draft';
                time.value = '';
                previewWrapper.classList.add('hidden');
            } else {
                form.action = '/admin/articles/update/' + data.id;
                title.value = data.title;
                content.value = data.content;
                image.value = data.image || '';
                thumb.value = data.thumbnail || '';
                category.value = data.kategori_slug;
                status.value = data.status;
                time.value = data.waktu_posting;

                if (data.thumbnail) {
                    preview.src = data.thumbnail;
                    previewWrapper.classList.remove('hidden');
                } else {
                    previewWrapper.classList.add('hidden');
                }
            }
        }

        function openEditArticle(data) {
            openArticleModal('edit', data);
        }

        document.getElementById('openArticleModal').onclick = () => openArticleModal();
        document.getElementById('closeArticleModal').onclick = () => modal.classList.add('hidden');
        document.getElementById('cancelArticleModal').onclick = () => modal.classList.add('hidden');

        thumb.addEventListener('input', () => {
            if (thumb.value) {
                preview.src = thumb.value;
                previewWrapper.classList.remove('hidden');
            } else {
                previewWrapper.classList.add('hidden');
            }
        });
    </script>

@endsection