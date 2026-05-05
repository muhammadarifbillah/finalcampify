<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Pembeli\Article_pembeli;

class PembeliArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->query('q');
        $articles = Article_pembeli::when($query, function ($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('content', 'like', "%{$query}%")
              ->orWhere('category', 'like', "%{$query}%");
        })->latest()->get();

        return view('pembeli.articles.index_pembeli', compact('articles', 'query'));
    }

    public function show($id)
    {
        $article = Article_pembeli::findOrFail($id);

        return view('pembeli.articles.show_pembeli', compact('article'));
    }
}