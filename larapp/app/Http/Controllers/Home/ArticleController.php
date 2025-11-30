<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $q = Article::query()->orderBy('published_at', 'desc');

        // basic filters (category, year) - extend as needed
        if ($request->filled('category')) {
            $q->whereHas('category', function($sub) use ($request) {
                $sub->where('slug', $request->query('category'));
            });
        }
        if ($request->filled('year')) {
            $q->whereYear('published_at', $request->query('year'));
        }

        $articles = $q->paginate(12)->withQueryString();

        // load region lists for filter selects
        $provinces = \App\Models\Regions::where('level', 1)->get();
        $witels = collect();
        $stos = collect();

        return view('home.article.index', compact('articles','provinces','witels','stos'));
    }
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
