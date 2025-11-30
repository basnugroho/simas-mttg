<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::query()->where('status', 'published');

        // Apply optional filters if provided (province/witel/sto via mosque relation)
        if ($request->filled('province_id') || $request->filled('witel_id') || $request->filled('sto_id')) {
            $query->whereHas('mosque', function ($q) use ($request) {
                if ($request->filled('province_id')) {
                    $q->where('province_id', $request->province_id);
                }
                if ($request->filled('witel_id')) {
                    $q->where('witel_id', $request->witel_id);
                }
                if ($request->filled('sto_id')) {
                    $q->where('sto_id', $request->sto_id);
                }
            });
        }

        $articles = $query->with(['category', 'mosque'])
            ->orderByDesc('published_at')
            ->paginate(9)
            ->withQueryString();

        // Load region filters from Region model if available
        $provinces = \App\Models\Region::where('level', 'PROVINCE')->orderBy('name')->get();
        $witels = collect();
        $stos = collect();

        return view('home.article.index', compact('articles', 'provinces', 'witels', 'stos'));
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
