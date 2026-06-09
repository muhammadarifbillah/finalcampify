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

        <div class="grid gap-6 md:grid-cols-3">
            {{-- Card 1: Total --}}
            <div class="bg-white border border-slate-200 hover:border-[#059669] hover:shadow-[0_12px_30px_-10px_rgba(5,150,105,0.2)] rounded-2xl p-6 shadow-sm transition-all duration-300 relative overflow-hidden flex flex-col justify-between group min-h-[140px]">
                <div class="absolute top-0 left-0 w-full h-[4px] bg-[#059669]"></div>
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Artikel</span>
                    <div class="w-8 h-8 rounded-lg bg-[#ecfdf5] text-[#059669] flex items-center justify-center transition-colors group-hover:bg-[#059669] group-hover:text-white">
                        <i data-lucide="library" class="w-4 h-4"></i>
                    </div>
                </div>
                <div>
                    <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ number_format($articles->count()) }}</h2>
                    <p class="text-[11px] text-slate-400 font-bold mt-1">Seluruh artikel sistem</p>
                </div>
            </div>

            {{-- Card 2: Draft --}}
            <div class="bg-white border border-slate-200 hover:border-[#b45309] hover:shadow-[0_12px_30px_-10px_rgba(180,83,9,0.2)] rounded-2xl p-6 shadow-sm transition-all duration-300 relative overflow-hidden flex flex-col justify-between group min-h-[140px]">
                <div class="absolute top-0 left-0 w-full h-[4px] bg-[#b45309]"></div>
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Draft</span>
                    <div class="w-8 h-8 rounded-lg bg-[#fffbeb] text-[#b45309] flex items-center justify-center transition-colors group-hover:bg-[#b45309] group-hover:text-white">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                    </div>
                </div>
                <div>
                    <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ number_format($draftCount) }}</h2>
                    <p class="text-[11px] text-slate-400 font-bold mt-1">Belum dipublikasikan</p>
                </div>
            </div>

            {{-- Card 3: Publish --}}
            <div class="bg-white border border-slate-200 hover:border-[#10b981] hover:shadow-[0_12px_30px_-10px_rgba(16,185,129,0.2)] rounded-2xl p-6 shadow-sm transition-all duration-300 relative overflow-hidden flex flex-col justify-between group min-h-[140px]">
                <div class="absolute top-0 left-0 w-full h-[4px] bg-[#10b981]"></div>
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Publish</span>
                    <div class="w-8 h-8 rounded-lg bg-[#f0fdf4] text-[#10b981] flex items-center justify-center transition-colors group-hover:bg-[#10b981] group-hover:text-white">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                    </div>
                </div>
                <div>
                    <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ number_format($publishCount) }}</h2>
                    <p class="text-[11px] text-slate-400 font-bold mt-1">Tampil di marketplace</p>
                </div>
            </div>
        </div>

        <div class="admin-card rounded-xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="admin-table w-full text-sm">
                    <thead class="bg-[#f8fbf9] border-b border-gray-100 text-gray-500 text-xs tracking-wider">
                        <tr>
                            <th class="py-4 px-4 text-left font-bold">JUDUL ARTIKEL</th>
                            <th class="py-4 px-4 text-left font-bold">KATEGORI</th>
                            <th class="py-4 px-4 text-center font-bold">STATUS</th>
                            <th class="py-4 px-4 text-left font-bold">TANGGAL</th>
                            <th class="py-4 px-4 text-center font-bold">VIEWS</th>
                            <th class="py-4 px-4 text-left font-bold">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($articles as $article)
                            <tr class="hover:bg-gray-50">
                                <td class="py-4 px-4">
                                    <div class="font-extrabold text-slate-800">{{ $article->title }}</div>
                                    <div class="text-xs text-slate-500 mt-1 line-clamp-1">{{ \Illuminate\Support\Str::limit($article->content, 90) }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-block bg-slate-100 text-slate-700 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wide">
                                        {{ $article->kategori_slug }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="inline-block px-2.5 py-1 text-[9px] font-bold tracking-wider rounded-full {{ $article->status === 'publish' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} uppercase">
                                        {{ $article->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="text-xs text-gray-700 font-semibold">{{ $article->waktu_posting->format('d M Y') }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $article->waktu_posting->format('H:i') }}</div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="font-bold text-slate-700 flex items-center justify-center gap-1">
                                        <i data-lucide="eye" class="w-3 h-3 text-slate-400"></i> {{ number_format($article->views) }}
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 min-w-[280px]">
                                        <a href="/admin/articles/show/{{ $article->id }}" class="admin-button admin-button-ghost py-1.5 text-xs flex justify-center">Lihat Real</a>
                                        <button onclick='openEditArticle(@json($article))' class="admin-button admin-button-primary py-1.5 text-xs flex justify-center" type="button">Edit</button>
                                        @if($article->status === 'draft')
                                            <a href="/admin/articles/publish/{{ $article->id }}" class="admin-button admin-button-primary bg-emerald-600 hover:bg-emerald-700 py-1.5 text-xs border-emerald-600 flex justify-center text-center">Publish</a>
                                        @else
                                            <a href="/admin/articles/unpublish/{{ $article->id }}" class="admin-button admin-button-ghost text-amber-600 hover:bg-amber-50 hover:text-amber-700 py-1.5 text-xs border border-amber-200 flex justify-center text-center">Unpublish</a>
                                        @endif
                                        <a href="/admin/articles/delete/{{ $article->id }}" onclick="return confirm('Yakin hapus artikel ini?')" class="admin-button admin-button-danger py-1.5 text-xs flex justify-center">Hapus</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-gray-400 text-sm">
                                    <i data-lucide="file-text" style="width: 32px; height: 32px;" class="mx-auto mb-2 opacity-30"></i>
                                    <p>Belum ada artikel ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="articleModal" class="fixed inset-0 hidden items-center justify-center bg-slate-900/40 backdrop-blur-sm z-50 p-4 transition-all">
        <div class="bg-white rounded-2xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.3)] w-full max-w-3xl max-h-[calc(100vh-3rem)] overflow-y-auto overflow-x-hidden border border-slate-100">
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 bg-slate-50/50 sticky top-0 z-10">
                <div>
                    <h2 id="articleModalTitle" class="text-xl font-black text-slate-800 flex items-center gap-2">
                        <i data-lucide="pen-tool" class="w-5 h-5 text-emerald-600"></i>
                        <span>Tambah Artikel</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 font-medium">Lengkapi seluruh kolom untuk mempublikasikan artikel.</p>
                </div>
                <button id="closeArticleModal" class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-700 transition-colors" type="button">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form id="articleForm" method="POST" action="/admin/articles/store" class="p-6 space-y-5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Judul Artikel</label>
                    <input id="articleTitle" type="text" name="title" class="admin-form-control rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20 shadow-sm" placeholder="Masukkan judul yang menarik..." required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Konten</label>
                    <textarea id="articleContent" name="content" class="admin-form-control rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20 shadow-sm" rows="6" placeholder="Tulis isi artikel di sini..." required></textarea>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kategori</label>
                        <select id="articleCategory" name="kategori_slug" class="admin-form-control rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20 shadow-sm" required>
                            <option value="">Pilih kategori</option>
                            <option value="outdoor">Outdoor</option>
                            <option value="tips">Tips</option>
                            <option value="review">Review</option>
                            <option value="panduan">Panduan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Status</label>
                        <select id="articleStatus" name="status" class="admin-form-control rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20 shadow-sm" required>
                            <option value="draft">Draft</option>
                            <option value="publish">Publish</option>
                        </select>
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Waktu Posting</label>
                        <input id="articleTime" type="datetime-local" name="waktu_posting" class="admin-form-control rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20 shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">URL Thumbnail</label>
                        <input id="articleThumbnail" type="text" name="thumbnail" class="admin-form-control rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20 shadow-sm" placeholder="https://..." required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">URL Gambar Tambahan (Opsional)</label>
                    <input id="articleImage" type="text" name="image" class="admin-form-control rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500/20 shadow-sm" placeholder="https://...">
                </div>

                <div id="thumbnailPreview" class="hidden mt-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                    <p class="text-xs font-bold text-slate-500 mb-3 flex items-center gap-2">
                        <i data-lucide="image" class="w-3 h-3"></i> PREVIEW THUMBNAIL
                    </p>
                    <img id="previewThumbnail" class="h-48 w-full rounded-lg object-cover border border-slate-200 shadow-sm">
                </div>

                <div class="flex justify-end gap-3 pt-4 mt-2 border-t border-slate-100">
                    <button id="cancelArticleModal" type="button" class="admin-button admin-button-ghost rounded-xl px-5">Batal</button>
                    <button class="admin-button admin-button-primary rounded-xl px-8 shadow-md shadow-emerald-500/20" type="submit">Simpan Artikel</button>
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

        // Auto-open edit modal if query parameter 'edit' exists
        const urlParams = new URLSearchParams(window.location.search);
        const editId = urlParams.get('edit');
        if (editId) {
            const articlesData = @json($articles);
            const articleToEdit = articlesData.find(a => a.id == editId);
            if (articleToEdit) {
                openEditArticle(articleToEdit);
            }
        }
    </script>
@endsection
