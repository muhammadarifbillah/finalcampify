<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::latest()->get();

        return view('admin.articles', [
            'articles' => $articles,
            'draftCount' => $articles->where('status', 'draft')->count(),
            'publishCount' => $articles->where('status', 'publish')->count(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|string|max:255',
            'waktu_posting' => 'required|date',
            'kategori_slug' => 'required|string|max:100',
            'status' => 'required|in:draft,publish',
            'thumbnail' => 'required|string|max:255',
            'views' => 'nullable|integer|min:0',
        ]);

        $data['views'] = $data['views'] ?? 0;

        Article::create($data);

        return redirect('/admin/articles')->with('success', 'Artikel berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|string|max:255',
            'waktu_posting' => 'required|date',
            'kategori_slug' => 'required|string|max:100',
            'status' => 'required|in:draft,publish',
            'thumbnail' => 'required|string|max:255',
            'views' => 'nullable|integer|min:0',
        ]);

        $article->update($data);

        return redirect('/admin/articles')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Article::findOrFail($id)->delete();
        return back()->with('success', 'Artikel berhasil dihapus.');
    }

    public function show($id)
    {
        $article = Article::findOrFail($id);

        return view('admin.article_detail', compact('article'));
    }

    public function publish($id)
    {
        $article = Article::findOrFail($id);
        $article->update(['status' => 'publish']);

        return back()->with('success', 'Artikel berhasil dipublish.');
    }

    public function unpublish($id)
    {
        $article = Article::findOrFail($id);
        $article->update(['status' => 'draft']);

        return back()->with('success', 'Artikel berhasil disimpan sebagai draft.');
    }

    public function publicShow($id)
    {
        $article = Article::where('status', 'publish')->findOrFail($id);
        $article->increment('views');

        return view('article_show', compact('article'));
    }
}