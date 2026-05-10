<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pembeli\Article_pembeli;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->query('q');
        $articles = Article_pembeli::when($query, function ($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('content', 'like', "%{$query}%")
              ->orWhere('category', 'like', "%{$query}%");
        })->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $articles
        ]);
    }

    public function show($id)
    {
        $article = Article_pembeli::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $article
        ]);
    }
}
