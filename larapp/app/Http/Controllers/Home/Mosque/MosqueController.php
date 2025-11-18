<?php

namespace App\Http\Controllers\Home\Mosque;

use App\Http\Controllers\Controller;
use App\Models\Mosque;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MosqueController extends Controller
{
    public function show(Mosque $mosque)
    {
        $mosque->load(['province','city','witel','mosqueFacility.facility']);

        // Map facilities to include available flag and note
        $facilities = $mosque->mosqueFacility->map(function ($mf) {
            return [
                'id' => $mf->id,
                'name' => $mf->facility->name ?? $mf->name ?? 'Unknown',
                'is_available' => (bool) $mf->is_available,
                'note' => $mf->note,
            ];
        })->toArray();

        // Enrich mosque object with convenience props used by the view
        $mosque->region_name = $mosque->city->name ?? ($mosque->province->name ?? null);
        $mosque->facilities = $facilities;
        $mosque->cover = $mosque->image_url ?? null;
        $mosque->short_description = Str::limit($mosque->description ?? '', 150);

        return view('home.mosque.detail.index', compact('mosque'));
    }

    
}
