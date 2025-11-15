<?php

namespace App\Http\Controllers\Home\Beranda;

use App\Http\Controllers\Controller;
use App\Models\Mosque;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Return JSON suggestions for autocomplete.
     */
    public function suggestions(Request $request)
    {
        $q = trim($request->get('q', ''));
        if ($q === '') {
            return response()->json([]);
        }
        $results = Mosque::query()
            ->where('name', 'like', '%' . $q . '%')
            ->orderBy('name')
            ->limit(7)
            ->get(['id', 'name', 'type']);

        return response()->json($results);
    }

    public function results(Request $request)
    {
        $q = trim($request->get('q', ''));
        $mosques = [];
        if ($q !== '') {
            $mosques = Mosque::query()
                ->where('name', 'like', '%' . $q . '%')
                ->orderBy('name')
                ->paginate(15)
                ->appends(['q' => $q]);
        }
        return view('home.mosque.detail', [
            'query' => $q,
            'mosques' => $mosques,
        ]);
    }
}
