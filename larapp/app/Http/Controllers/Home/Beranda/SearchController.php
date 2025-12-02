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
        $results = Mosque::with(['city','witel'])
            ->where(function($qb) use ($q){
                $qb->where('name', 'like', '%' . $q . '%')
                   ->orWhereHas('witel', function($wq) use ($q){
                       $wq->where('name', 'like', '%' . $q . '%');
                   })
                   ->orWhereHas('city', function($cq) use ($q){
                       $cq->where('name', 'like', '%' . $q . '%');
                   });
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'type', 'city_id', 'witel_id'])
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'name' => $m->name,
                    'type' => $m->type,
                    'city' => $m->city ? $m->city->name : '',
                    'witel' => $m->witel ? $m->witel->name : '',
                ];
            });

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
        return view('home.mosque.search', [
            'query' => $q,
            'mosques' => $mosques,
        ]);
    }
}
