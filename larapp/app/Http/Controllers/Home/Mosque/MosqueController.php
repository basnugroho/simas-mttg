<?php

namespace App\Http\Controllers\Home\Mosque;

use App\Http\Controllers\Controller;
use App\Models\Mosque;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MosqueController extends Controller
{
    public function index()
    {
        $query = Mosque::query()->where('is_active', true)->with(['city','province','witel']);

        $provinceId = request()->query('province_id');
        $cityId = request()->query('city_id');
        $witelId = request()->query('witel_id');
        $stoId = request()->query('sto_id');
        $facilityId = request()->query('facility_id');
        $type = request()->query('type');
        $q = request()->query('q');

        if ($provinceId) {
            $query->where('province_id', $provinceId);
        }

        if ($cityId) {
            $query->where('city_id', $cityId);
        }

        if ($witelId) {
            $query->where('witel_id', $witelId);
        }

        if ($stoId) {
            $query->where('sto_id', $stoId);
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($facilityId) {
            $query->whereHas('facility', function ($sub) use ($facilityId) {
                $sub->where('facilities.id', $facilityId);
            });
        }

        if ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('address', 'like', "%{$q}%");
            });
        }

        $mosques = $query->orderBy('name')->paginate(12)->withQueryString();

        // Options for filters
        $provinces = \App\Models\Regions::where('level', 'AREA')->orderBy('name')->get();

        // If a province is selected, scope cities and witels to that province's direct children
        if ($provinceId) {
            $witels = \App\Models\Regions::where('parent_id', $provinceId)->where('level', 'WITEL')->orderBy('name')->get();
        } else {
            // no province selected: provide full list of witels so the select remains usable
            $witels = \App\Models\Regions::where('level', 'WITEL')->orderBy('name')->get();
        }

        // STOs: if a witel selected, scope to that parent; otherwise provide all STOs
        if ($witelId) {
            $stos = \App\Models\Regions::where('parent_id', $witelId)->where('level', 'STO')->orderBy('name')->get();
        } else {
            $stos = \App\Models\Regions::where('level', 'STO')->orderBy('name')->get();
        }

        $facilities = \App\Models\Facility::orderBy('name')->get();

        return view('home.mosque.index', compact('mosques', 'provinces', 'witels', 'stos', 'facilities'));
    }
    public function show(Mosque $mosque)
    {
        $mosque->load(['province','city','witel','mosqueFacility.facility','photos']);

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
