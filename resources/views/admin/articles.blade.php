@extends('layouts.admin')

@section('title', 'Articles Admin')

@section('content')
    <div class="space-y-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="admin-section-title">Artikel</h1>
                <p class="admin-section-subtitle">Kelola draft dan publish article marketplace.</p>
            </div>
            <button id="openArticleModal" class="admin-button admin-button-primary" type="button">
                <i data-lucide="plus"></i>
                Tambah Artikel
            </button>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
            <div class="admin-card admin-stat-card"><p class="admin-stat-label">Total</p><h2 class="admin-stat-value">{{ $articles->count() }}</h2></div>
            <div class="admin-card admin-stat-card"><p class="admin-stat-label">Draft</p><h2 class="admin-stat-value">{{ $draftCount }}</h2></div>
            <div class="admin-card admin-stat-card"><p class="admin-stat-label">Publish</p><h2 class="admin-stat-value">{{ $publishCount }}</h2></div>
        </div>

        <div class="admin-card">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Posting</th>
                            <th>Views</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($articles as $article)
                            <tr>
                                <td>
                                    <div class="font-extrabold">{{ $article->title }}</div>
                                    <div class="text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($article->content, 90) }}</div>
                                </td>
                                <td>{{ $article->kategori_slug }}</td>
                                <td><span class="admin-badge {{ $article->status === 'publish' ? 'admin-badge-success' : 'admin-badge-warning' }}">{{ $article->status }}</span></td>
                                <td>{{ $article->waktu_posting->format('d M Y') }}</td>
                                <td>{{ $article->views }}</td>
                                <td>
                                    <div class="flex flex-wrap gap-2">
                                        <a href="/admin/articles/show/{{ $article->id }}" class="admin-button admin-button-ghost">Detail</a>
                                        <button onclick='openEditArticle(@json($article))' class="admin-button admin-button-primary" type="button">Edit</button>
                                        @if($article->status === 'draft')
                                            <a href="/admin/articles/publish/{{ $article->id }}" class="admin-button admin-button-primary">Publish</a>
                                        @else
                                            <a href="/admin/articles/unpublish/{{ $article->id }}" class="admin-button admin-button-ghost">Unpublish</a>
                                        @endif
                                        <a href="/admin/articles/delete/{{ $article->id }}" onclick="return confirm('Yakin hapus artikel ini?')" class="admin-button admin-button-danger">Hapus</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><div class="admin-empty">Belum ada artikel.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="articleModal" class="fixed inset-0 hidden items-center justify-center bg-black/40 z-50 p-4">
        <div class="admin-card w-full max-w-2xl max-h-[calc(100vh-3rem)] overflow-y-auto p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 id="articleModalTitle" class="text-2xl font-extrabold">Tambah Artikel</h2>
                    <p class="text-sm text-slate-500">Form artikel lengkap.</p>
                </div>
                <button id="closeArticleModal" class="admin-button admin-button-ghost" type="button">Tutup</button>
            </div>

            <form id="articleForm" method="POST" action="/admin/articles/store" class="space-y-4">
                @csrf
                <input id="articleTitle" type="text" name="title" class="admin-form-control" placeholder="Judul artikel" required>
                <textarea id="articleContent" name="content" class="admin-form-control" rows="6" placeholder="Konten" required></textarea>
                <div class="grid gap-4 md:grid-cols-2">
                    <select id="articleCategory" name="kategori_slug" class="admin-form-control" required>
                        <option value="">Pilih kategori</option>
                        <option value="outdoor">Outdoor</option>
                        <option value="tips">Tips</option>
                        <option value="review">Review</option>
                        <option value="panduan">Panduan</option>
                    </select>
                    <select id="articleStatus" name="status" class="admin-form-control" required>
                        <option value="draft">Draft</option>
                        <option value="publish">Publish</option>
                    </select>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <input id="articleTime" type="datetime-local" name="waktu_posting" class="admin-form-control" required>
                    <input id="articleThumbnail" type="text" name="thumbnail" class="admin-form-control" placeholder="URL thumbnail" required>
                </div>
                <input id="articleImage" type="text" name="image" class="admin-form-control" placeholder="URL gambar tambahan">

                <div id="thumbnailPreview" class="hidden">
                    <p class="text-sm text-slate-500 mb-2">Preview</p>
                    <img id="previewThumbnail" class="h-40 w-full rounded-lg object-cover">
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button id="cancelArticleModal" type="button" class="admin-button admin-button-ghost">Batal</button>
                    <button class="admin-button admin-button-primary" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
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
                return;
            }

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
            }
        }

        function openEditArticle(data) { openArticleModal('edit', data); }
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
