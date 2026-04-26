@extends('layouts.admin')

@section('content')

    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Manajemen Artikel</h1>
            <p class="text-gray-600">Kelola artikel, tambah konten baru, dan edit langsung dari pop-up modal.</p>
        </div>
        <button id="openArticleModal"
            class="bg-green-700 text-white px-5 py-3 rounded-xl font-semibold hover:bg-green-800">Tambah Artikel</button>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-50 border border-green-200 p-4 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-4 text-red-800">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div id="articleModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
        <div class="w-full max-w-2xl rounded-3xl bg-white p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h2 id="articleModalTitle" class="text-xl font-semibold">Tambah Artikel</h2>
                <button type="button" id="closeArticleModal" class="text-gray-500 hover:text-gray-900">Tutup</button>
            </div>
            <form id="articleForm" method="POST" action="/admin/articles/store" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul</label>
                    <input id="articleTitle" type="text" name="title"
                        class="w-full border rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Judul artikel" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Konten</label>
                    <textarea id="articleContent" name="content" rows="5"
                        class="w-full border rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Isi artikel"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gambar (opsional)</label>
                    <input id="articleImage" type="text" name="image"
                        class="w-full border rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="URL gambar" />
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" id="cancelArticleModal"
                        class="bg-gray-200 text-gray-700 px-5 py-3 rounded-xl">Batal</button>
                    <button id="articleSubmit" type="submit"
                        class="bg-green-700 text-white px-5 py-3 rounded-xl">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="space-y-4">
        @forelse($articles as $a)
            <div class="bg-white p-5 rounded-xl shadow flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <h2 class="font-bold text-lg">{{ $a->title }}</h2>
                    <p class="text-gray-500 mt-2">{{ $a->content }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick='openEditArticle(@json($a))'
                        class="bg-blue-600 text-white px-4 py-2 rounded-xl text-sm">Edit</button>
                    <a href="/admin/articles/delete/{{ $a->id }}" class="bg-red-600 text-white px-4 py-2 rounded-xl text-sm"
                        onclick="return confirm('Yakin ingin hapus artikel ini?')">Hapus</a>
                </div>
            </div>
        @empty
            <div class="bg-white p-5 rounded-xl shadow text-gray-500">Belum ada artikel. Tambahkan artikel baru untuk mulai.
            </div>
        @endforelse
    </div>

    <script>
        const articleModal = document.getElementById('articleModal');
        const articleForm = document.getElementById('articleForm');
        const articleModalTitle = document.getElementById('articleModalTitle');
        const articleSubmit = document.getElementById('articleSubmit');
        const articleTitle = document.getElementById('articleTitle');
        const articleContent = document.getElementById('articleContent');
        const articleImage = document.getElementById('articleImage');
        const openArticleModalButton = document.getElementById('openArticleModal');
        const closeArticleModalButton = document.getElementById('closeArticleModal');
        const cancelArticleModalButton = document.getElementById('cancelArticleModal');

        function openArticleModal(mode = 'add', article = null) {
            articleModal.classList.remove('hidden');
            if (mode === 'add') {
                articleModalTitle.textContent = 'Tambah Artikel';
                articleForm.action = '/admin/articles/store';
                articleSubmit.textContent = 'Tambah';
                articleTitle.value = '';
                articleContent.value = '';
                articleImage.value = '';
            } else {
                articleModalTitle.textContent = 'Edit Artikel';
                articleForm.action = '/admin/articles/update/' + article.id;
                articleSubmit.textContent = 'Simpan';
                articleTitle.value = article.title;
                articleContent.value = article.content;
                articleImage.value = article.image || '';
            }
        }

        function closeArticleModal() {
            articleModal.classList.add('hidden');
        }

        function openEditArticle(article) {
            openArticleModal('edit', article);
        }

        openArticleModalButton.addEventListener('click', () => openArticleModal('add'));
        closeArticleModalButton.addEventListener('click', closeArticleModal);
        cancelArticleModalButton.addEventListener('click', closeArticleModal);
        window.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !articleModal.classList.contains('hidden')) {
                closeArticleModal();
            }
        });
    </script>

@endsection