<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    public function show($id)
    {
        $article = Article::findOrFail($id);

        // Simple related articles query: latest articles excluding current
        $related = Article::where('id', '!=', $article->id)
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();

        return view('home.article.detail.index', [
            'article' => $article,
            'related' => $related,
        ]);
    }
}
